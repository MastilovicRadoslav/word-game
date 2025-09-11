// src/features/words/wordsSlice.js
import { createSlice, nanoid } from '@reduxjs/toolkit'

const initialState = {
  items: [],        // [{id, word, normalized, uniqueLetters, isPalindrome, isAlmostPalindrome, score, addedAt}]
  lastAddedId: null,
}

const wordsSlice = createSlice({
  name: 'words',
  initialState,
  reducers: {
    addWord(state, action) {
      const p = action.payload
      const item = {
        id: p.id ?? nanoid(),
        word: p.word,
        normalized: p.normalized,
        uniqueLetters: p.uniqueLetters,
        isPalindrome: p.isPalindrome,
        isAlmostPalindrome: p.isAlmostPalindrome,
        score: p.score,
        addedAt: p.addedAt ?? Date.now(),
      }
      state.items.push(item)
      state.lastAddedId = item.id
    },
    removeById(state, action) {
      const id = action.payload
      state.items = state.items.filter((x) => x.id !== id)
      if (state.lastAddedId === id) state.lastAddedId = null
    },
    clear(state) {
      state.items = []
      state.lastAddedId = null
    },
  },
})

export const { addWord, removeById, clear } = wordsSlice.actions
export default wordsSlice.reducer

// SELECTORS
export const selectWordsSlice = (state) => state.words
export const selectSortedWords = (state) =>
  [...state.words.items].sort((a, b) => b.score - a.score)
