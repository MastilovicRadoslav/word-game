// src/features/words/wordsSlice.js
import { createSlice, nanoid } from '@reduxjs/toolkit'

const initialState = {
  items: [],        // [{id, word, normalized, uniqueLetters, isPalindrome, isAlmostPalindrome, score, addedAt}]
  lastAddedId: null, // ID posljednje dodate riječi (da možeš u UI highlightovati tu riječ)
}

// naziv slice-a (name: 'words')
// početno stanje
// redukere (funkcije koje mijenjaju stanje)
// automatski generiše akcije (addWord, removeById, clear).

const wordsSlice = createSlice({
  name: 'words',
  initialState,
  reducers: {
    addWord(state, action) { // Prima payload (podatke o riječi); Pravi novi objekat; Dodaje ga u state.items; Postavlja lastAddedId na taj novi ID.
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
    removeById(state, action) { // Prima id u payload-u.; Filtrira items da ukloni riječ sa tim id-jem.; Ako si obrisao baš onu koja je bila lastAddedId, resetuje ga na null.
      const id = action.payload
      state.items = state.items.filter((x) => x.id !== id)
      if (state.lastAddedId === id) state.lastAddedId = null
    },
    clear(state) { // Briše cijelu listu (items = []); Resetuje lastAddedId.
      state.items = []
      state.lastAddedId = null
    },
  },
})

// addWord, removeById, clear → akcije koje možeš dispatchovati u komponentama.
export const { addWord, removeById, clear } = wordsSlice.actions
export default wordsSlice.reducer // wordsSlice.reducer ide u store.

// SELECTORS
export const selectWordsSlice = (state) => state.words // Vrati cijeli slice (items + lastAddedId).

// Uvijek vraća kopiju items sortiranih po score-u (od najvećeg ka najmanjem).
// Memoizacija znači da će vrati istu referencu dok se items ne promijeni.import { createSelector } from '@reduxjs/toolkit'
export const selectSortedWords = createSelector(
  (state) => state.words.items,
  (items) => [...items].sort((a, b) => b.score - a.score)
)

