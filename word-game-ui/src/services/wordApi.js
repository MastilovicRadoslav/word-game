// src/services/wordApi.js
import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react'

export const wordApi = createApi({
  reducerPath: 'wordApi',
  baseQuery: fetchBaseQuery({ baseUrl: '/' }),
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
