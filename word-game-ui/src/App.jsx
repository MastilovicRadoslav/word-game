import { Layout, Typography, Card, Divider } from 'antd'
import WordForm from './components/WordForm'
import WordList from './components/WordList'
import './App.css'   // ✅ uvoz CSS-a

const { Header, Content, Footer } = Layout
const { Title, Text } = Typography

export default function App() {
  return (
    <Layout className="site-layout">
      <Header className="site-header">
        <div className="header-inner">
          <Title level={3} style={{ margin: 0 }}>Word Game</Title>
          <Text type="secondary" className="subtitle">Score & sort English words</Text>
        </div>
      </Header>

      <Content className="site-content">
        <div className="container">
          <Card className="card-block" bodyStyle={{ padding: 16 }}>
            <Title level={4} style={{ marginBottom: 8 }}>Add a word</Title>
            <Text type="secondary">Only valid English words are accepted by the backend.</Text>
            <Divider style={{ margin: '12px 0' }} />
            <WordForm />
          </Card>

          <Card className="card-block" bodyStyle={{ padding: 0 }}>
            <div className="list-header">
              <Title level={4} style={{ margin: 0 }}>Results</Title>
              <Text type="secondary">Sorted by score (desc). Recently added is highlighted.</Text>
            </div>
            <Divider style={{ margin: 0 }} />
            <div className="list-body">
              <WordList />
            </div>
          </Card>
        </div>
      </Content>

      <Footer className="site-footer">
        <Text type="secondary">© {new Date().getFullYear()} Word Game</Text>
      </Footer>
    </Layout>
  )
}
