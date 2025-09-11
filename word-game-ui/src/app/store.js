// src/app/store.js
import { configureStore, combineReducers } from '@reduxjs/toolkit'
import { persistStore, persistReducer } from 'redux-persist'
import storage from 'redux-persist/lib/storage'
import wordsReducer from '../features/words/wordsSlice'
import { wordApi } from '../services/wordApi'

const rootReducer = combineReducers({
  words: wordsReducer,
  [wordApi.reducerPath]: wordApi.reducer,
})

const persistConfig = {
  key: 'word-game',
  storage,
  whitelist: ['words'], // ne persistamo RTK Query cache
}

const persisted = persistReducer(persistConfig, rootReducer)

export const store = configureStore({
  reducer: persisted,
  middleware: (getDefault) =>
    getDefault({
      serializableCheck: false,
    }).concat(wordApi.middleware),
})

export const persistor = persistStore(store)
