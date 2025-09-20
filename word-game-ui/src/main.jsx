// src/main.jsx
import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import { Provider } from 'react-redux'
import { PersistGate } from 'redux-persist/integration/react'
import { store, persistor } from './app/store.js'
import 'antd/dist/reset.css'
import './index.css'

// <Provider store={store}> - čini Redux store dostupnim kroz cijelu app (hooks useDispatch, useSelector).
// <PersistGate loading={null} persistor={persistor}> - čeka da rehidrira stanje iz localStorage prije rendera (da lista riječi preživi reload).
ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <Provider store={store}> 
      <PersistGate loading={null} persistor={persistor}>
        <App />
      </PersistGate>
    </Provider>
  </React.StrictMode>
)
