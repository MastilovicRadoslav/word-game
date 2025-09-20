// src/app/store.js - centralno stanje
import { configureStore, combineReducers } from '@reduxjs/toolkit'
import { persistStore, persistReducer } from 'redux-persist'
import storage from 'redux-persist/lib/storage'
import wordsReducer from '../features/words/wordsSlice'
import { wordApi } from '../services/wordApi'

const rootReducer = combineReducers({
  words: wordsReducer, // lista riječi – ručno održavanje
  [wordApi.reducerPath]: wordApi.reducer, // (RTK Query cache za pozive ka backendu)
})

const persistConfig = { // (čuva dio stanja u localStorage) → da lista u browseru preživi refresh
  key: 'word-game',
  storage, // // localStorage
  whitelist: ['words'], // čuvaj SAMO slice "words"
}

const persisted = persistReducer(persistConfig, rootReducer)

export const store = configureStore({
  reducer: persisted,
  middleware: (getDefault) => // RTK Query (da API hookovi rade: caching, refetch, lifecycle).
    getDefault({
      serializableCheck: false,
    }).concat(wordApi.middleware),
})

export const persistor = persistStore(store)
