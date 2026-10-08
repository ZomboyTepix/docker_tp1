import React from 'react';

function App() {
  return (
    <div style={{
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      justifyContent: 'center',
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #1a1a2e, #16213e, #0f3460)',
      color: '#e0e0e0',
      fontFamily: 'Arial, sans-serif'
    }}>
      <h1 style={{ fontSize: '3rem', color: '#e94560' }}>
        Hello World
      </h1>
    </div>
  );
}

export default App;
