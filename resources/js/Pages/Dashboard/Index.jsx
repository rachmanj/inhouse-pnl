import { Head } from '@inertiajs/react';
import { Col, Row } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import TrendLine from '@/Components/Charts/TrendLine';
import InsightsFeed from '@/Components/Shared/InsightsFeed';
import KpiCard from '@/Components/Shared/KpiCard';
import PeriodSelector from '@/Components/Shared/PeriodSelector';
import SiteStatusBoard from '@/Components/Shared/SiteStatusBoard';
import { ProCard } from '@ant-design/pro-components';

export default function Dashboard({
    title = 'Dashboard',
    kpis = {},
    trend = [],
    sites = [],
    insights = [],
}) {
    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <div style={{ marginBottom: 16 }}>
                <PeriodSelector />
            </div>

            <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
                <Col xs={24} sm={12} lg={6}>
                    <KpiCard
                        title="Revenue"
                        value={kpis.revenue ?? 0}
                        changePercent={kpis.revenue_change_percent}
                    />
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <KpiCard
                        title="Cost IPH"
                        value={kpis.cost_iph ?? 0}
                        changePercent={kpis.cost_iph_change_percent}
                    />
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <KpiCard
                        title="Net P&L"
                        value={kpis.net_pnl ?? 0}
                        changePercent={kpis.net_pnl_change_percent}
                    />
                </Col>
                <Col xs={24} sm={12} lg={6}>
                    <KpiCard
                        title="vs Baseline"
                        value={kpis.baseline_variance_percent != null ? `${kpis.baseline_variance_percent.toFixed(1)}%` : '—'}
                        suffix={null}
                    />
                </Col>
            </Row>

            <ProCard title="24-Month Trend" bordered style={{ marginBottom: 24 }}>
                <TrendLine data={trend} />
            </ProCard>

            <Row gutter={[16, 16]}>
                <Col xs={24} lg={14}>
                    <InsightsFeed insights={insights} />
                </Col>
                <Col xs={24} lg={10}>
                    <SiteStatusBoard sites={sites} />
                </Col>
            </Row>
        </AuthenticatedLayout>
    );
}
