// src/App.jsx
import { Layout, Typography } from 'antd'
import WordForm from './components/WordForm'
import WordList from './components/WordList'

const { Header, Content, Footer } = Layout
const { Title, Text } = Typography

export default function App() {
  return (
    <Layout style={{ minHeight: '100vh' }}>
      <Header style={{ background: 'white', borderBottom: '1px solid #f0f0f0' }}>
        <Title level={3} style={{ margin: 0 }}>Word Game</Title>
      </Header>

      <Content style={{ padding: 24, maxWidth: 900, margin: '0 auto', width: '100%' }}>
        <WordForm />
        <div style={{ height: 16 }} />
        <WordList />
      </Content>

      <Footer style={{ textAlign: 'center' }}>
        <Text type="secondary">Sorted by score (desc). Recently added is highlighted.</Text>
      </Footer>
    </Layout>
  )
}
