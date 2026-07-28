import { Head } from '@inertiajs/react';
import { Card, Col, Row, Statistic } from 'antd';
import { ArrowDownOutlined, ArrowUpOutlined } from '@ant-design/icons';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Index({ period, kpis, sites, insights }) {
    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />
            <Row gutter={[16, 16]}>
                <Col xs={24} sm={12} lg={6}>
                    <Card><Statistic title="Revenue" value={formatCurrency(kpis.revenue)} /></Card>
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <Card><Statistic title="Cost IPH" value={formatCurrency(kpis.cost_iph)} /></Card>
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <Card><Statistic title="Net P&L" value={formatCurrency(kpis.net_pnl)} valueStyle={{ color: kpis.net_pnl >= 0 ? '#3f8600' : '#cf1322' }} /></Card>
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <Card>
                        <Statistic title="vs 2024" value={kpis.vs_baseline_pct} suffix="%" prefix={kpis.vs_baseline_pct >= 0 ? <ArrowUpOutlined /> : <ArrowDownOutlined />}
                            valueStyle={{ color: kpis.vs_baseline_pct >= 0 ? '#3f8600' : '#cf1322' }} />
                    </Card>
                </Col>
            </Row>
            <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
                <Col xs={24} lg={16}>
                    <Card title="24-Month Trend"><p style={{ color: '#888' }}>Trend chart — data populates after imports</p></Card>
                </Col>
                <Col xs={24} lg={8}>
                    <Card title="Insights Feed">
                        {insights?.length ? insights.map((i, idx) => <p key={idx}>{i.message}</p>) : <p style={{ color: '#888' }}>No alerts</p>}
                    </Card>
                </Col>
            </Row>
            <Card title="Site Status Board" style={{ marginTop: 16 }}>
                <Row gutter={[8, 8]}>
                    {sites.map((s) => (
                        <Col key={s.code} xs={12} sm={8} md={6} lg={4}>
                            <Card size="small" title={s.code}><p>{s.name}</p><p>Status: {s.status}</p></Card>
                        </Col>
                    ))}
                </Row>
            </Card>
        </AuthenticatedLayout>
    );
}
