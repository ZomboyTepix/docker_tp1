const fs = require('node:fs/promises');
const path = require('node:path');
const { ESLint } = require('eslint');

const reportDirectory = process.env.LINT_REPORT_DIR || '/reports';
const reportPath = path.join(reportDirectory, 'lint.txt');

process.once('SIGTERM', () => {
  process.stderr.write('Analyse ESLint interrompue par SIGTERM.\n');
  process.exit(143);
});

async function run() {
  await fs.mkdir(reportDirectory, { recursive: true });
  await fs.writeFile(reportPath, 'Analyse ESLint en cours.\n');

  const eslint = new ESLint({ fix: false, cache: false });
  const results = await eslint.lintFiles(['src/**/*.{js,jsx}']);
  const formatter = await eslint.loadFormatter('stylish');
  const details = formatter.format(results);
  const errors = results.reduce((total, result) => total + result.errorCount, 0);
  const warnings = results.reduce((total, result) => total + result.warningCount, 0);
  const summary = `ESLint : ${results.length} fichier(s), ${errors} erreur(s), ${warnings} avertissement(s).\n`;
  const report = details + summary;

  await fs.writeFile(reportPath, report);
  process.stdout.write(report);
  process.exitCode = errors > 0 || warnings > 0 ? 1 : 0;
}

run().catch(async (error) => {
  const message = `ESLint : impossible de terminer l'analyse.\n${error.message}\n`;
  process.stderr.write(message);

  try {
    await fs.mkdir(reportDirectory, { recursive: true });
    await fs.writeFile(reportPath, message);
  } catch (reportError) {
    process.stderr.write(`Rapport non enregistré : ${reportError.message}\n`);
  }

  process.exitCode = 2;
});
