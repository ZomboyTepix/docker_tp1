#!/bin/sh
set -eu

# Le script utilise toujours le dossier du projet, même lancé depuis ailleurs.
dossier_projet=$(CDPATH= cd "$(dirname "$0")" && pwd)
cd "$dossier_projet"

attente_max="${PIPELINE_WAIT_TIMEOUT:-180}"
pid_commande=""
conteneur_actif=""

case "$attente_max" in
    ''|0*|*[!0-9]*)
        printf '%s\n' 'PIPELINE_WAIT_TIMEOUT doit être un entier strictement positif.' >&2
        exit 2
        ;;
esac

arreter_pipeline() {
    code_sortie="$1"
    trap '' INT TERM

    # On arrête seulement le contrôle CI lancé par cette exécution.
    if [ -n "$conteneur_actif" ]; then
        docker stop -t 10 "$conteneur_actif" >/dev/null 2>&1 || true
    fi

    if [ -n "$pid_commande" ]; then
        kill -TERM "$pid_commande" 2>/dev/null || true
        wait "$pid_commande" 2>/dev/null || true
    fi

    printf '%s\n' 'Pipeline interrompue.' >&2
    exit "$code_sortie"
}

trap 'arreter_pipeline 130' INT
trap 'arreter_pipeline 143' TERM

executer_etape() {
    printf '\n%s\n' "$1"
    shift

    # Le lancement en arrière-plan permet de gérer les signaux pendant l'attente.
    docker compose -f docker-compose.yml "$@" &
    pid_commande=$!

    if wait "$pid_commande"; then
        pid_commande=""
    else
        code_sortie=$?
        pid_commande=""
        printf 'Échec de cette étape (code %s). Pipeline arrêtée.\n' "$code_sortie" >&2
        exit "$code_sortie"
    fi
}

executer_etape 'Vérification de la configuration' --profile ci config --quiet

# Les rapports présents doivent correspondre à cette exécution.
mkdir -p reports
rm -f reports/lint.txt reports/junit.xml

executer_etape 'Construction des images de CI' --profile ci build lint test

conteneur_actif="tp-docker-pipeline-lint-$$"
executer_etape 'Contrôle du code React' \
    run --rm --no-deps -T --name "$conteneur_actif" lint
conteneur_actif=""

conteneur_actif="tp-docker-pipeline-test-$$"
executer_etape 'Tests de l’API Symfony' \
    run --rm --no-deps -T --name "$conteneur_actif" test
conteneur_actif=""

# Les images applicatives sont construites après la réussite des deux contrôles.
executer_etape 'Construction des images applicatives' build frontend backend nginx
executer_etape 'Démarrage et vérification des services' \
    up -d --no-build --wait --wait-timeout "$attente_max" frontend backend nginx

adresse=$(docker compose -f docker-compose.yml port nginx 80)
printf '\nPipeline réussie. Application : http://%s\n' "$adresse"
printf '%s\n' 'Rapports : reports/lint.txt et reports/junit.xml'
