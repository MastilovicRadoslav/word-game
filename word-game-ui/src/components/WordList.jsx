// src/components/WordList.jsx
import { Table, Tag, Button } from 'antd'
import { useAppDispatch, useAppSelector } from '../hooks'
import { clear, selectSortedWords, selectWordsSlice, removeById } from '../features/words/wordsSlice'

export default function WordList() {
  const dispatch = useAppDispatch()
  const sorted = useAppSelector(selectSortedWords)
  const { lastAddedId } = useAppSelector(selectWordsSlice)

  const columns = [
    { title: '#', render: (_,_r,i) => i + 1, width: 60 },
    { title: 'Word', dataIndex: 'word' },
    { title: 'Normalized', dataIndex: 'normalized' },
    { title: 'Unique', dataIndex: 'uniqueLetters', width: 90 },
    { title: 'Pal', dataIndex: 'isPalindrome', render: (v) => v ? <Tag>palindrome</Tag> : '-' },
    { title: 'Almost', dataIndex: 'isAlmostPalindrome', render: (v) => v ? <Tag color="processing">almost</Tag> : '-' },
    {
      title: 'Score',
      dataIndex: 'score',
      width: 100,
      sorter: (a, b) => a.score - b.score,
      defaultSortOrder: 'descend',
    },
    {
      title: '',
      width: 120,
      render: (record) => (
        <Button size="small" danger onClick={() => dispatch(removeById(record.id))}>
          Remove
        </Button>
      ),
    },
  ]

  return (
    <>
      <div style={{ margin: '16px 0', display: 'flex', justifyContent: 'flex-end' }}>
        <Button onClick={() => dispatch(clear())}>Clear list</Button>
      </div>

      <Table
        rowKey="id"
        dataSource={sorted}
        columns={columns}
        pagination={false}
        rowClassName={(record) => record.id === lastAddedId ? 'row-highlight' : ''}
      />
    </>
  )
}
