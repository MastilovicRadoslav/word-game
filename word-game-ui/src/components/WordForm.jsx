// src/components/WordForm.jsx
import { Form, Input, Button, Space, Typography, message } from 'antd'
import { useScoreWordMutation } from '../services/wordApi'
import { addWord } from '../features/words/wordsSlice'
import { useAppDispatch } from '../hooks'

function makeId() {
  // za highlight redova
  return globalThis.crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
}

export default function WordForm() {
  const [form] = Form.useForm()
  const [scoreWord, { isLoading }] = useScoreWordMutation() // RTK Query mutation hook; scoreWord je funkcija za API poziv; isLoading = true dok traje request. 
  const dispatch = useAppDispatch()

  const onFinish = async (values) => {
    const word = values.word?.trim()
    if (!word) return
    try {
      const res = await scoreWord(word).unwrap() // Ako input nije prazan, šalje ga API-ju.
      dispatch(addWord({ ...res, id: makeId(), addedAt: Date.now() })) // Dodaje riječ u Redux store (addWord) sa generisanim ID-jem i timestamp-om.
      alert(`Dodano: "${res.normalized}" (score ${res.score})`)
      form.resetFields()
    } catch (err) {
      const msg = err?.data?.error ?? 'Greška pri pozivu API-ja.'
      alert(msg)
    }
  }

  return (
    <Form form={form} layout="inline" onFinish={onFinish} style={{ gap: 8, flexWrap: 'wrap' }}>
      <Form.Item
        name="word"
        rules={[{ required: true, message: 'Upiši riječ' }]}
      >
        <Input placeholder="npr. level" autoFocus disabled={isLoading} />
      </Form.Item>
      <Form.Item>
        <Space>
          <Button type="primary" htmlType="submit" loading={isLoading}>
            Score
          </Button>
          <Typography.Text type="secondary">
            Backend: POST /api/words/score
          </Typography.Text>
        </Space>
      </Form.Item>
    </Form>
  )
}
