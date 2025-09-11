// src/services/wordApi.js
import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

const API = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000'

export const wordApi = createApi({
  reducerPath: 'wordApi',
  baseQuery: fetchBaseQuery({ baseUrl: API }),
  endpoints: (builder) => ({
    scoreWord: builder.mutation({
      query: (word) => ({
        url: '/api/words/score',
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: { word },
      }),
    }),
  }),
})

export const { useScoreWordMutation } = wordApi
