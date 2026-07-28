import { Head, Link } from '@inertiajs/react';
import { ProCard } from '@ant-design/pro-components';
import { Col, Row, Typography } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PeriodSelector from '@/Components/Shared/PeriodSelector';
import StatusChip from '@/Components/Shared/StatusChip';
import { formatCurrency } from '@/Utils/currency';

export default function Index({ funds = [] }) {
    return (
        <AuthenticatedLayout title="Petty Cash">
            <Head title="Petty Cash" />
            <div style={{ marginBottom: 16 }}>
                <PeriodSelector />
            </div>

            <Row gutter={[16, 16]}>
                {funds.map((fund) => (
                    <Col key={fund.id} xs={24} sm={12} lg={8}>
                        <ProCard
                            bordered
                            title={fund.project_site?.code ?? fund.site?.code}
                            subTitle={fund.project_site?.name ?? fund.site?.name}
                            extra={<StatusChip status={fund.status} />}
                            actions={[
                                <Link key="expenses" href={route('petty-cash.expenses.index', fund.id)}>
                                    View Expenses
                                </Link>,
                            ]}
                        >
                            <Typography.Paragraph>
                                Opening: {formatCurrency(fund.opening_balance)}
                            </Typography.Paragraph>
                            <Typography.Paragraph>
                                Replenishment: {formatCurrency(fund.replenishment_amount)}
                            </Typography.Paragraph>
                            <Typography.Title level={4} style={{ margin: 0 }}>
                                Closing: {formatCurrency(fund.closing_balance)}
                            </Typography.Title>
                        </ProCard>
                    </Col>
                ))}
            </Row>

            {funds.length === 0 && (
                <Typography.Text type="secondary">No petty cash funds for this period.</Typography.Text>
            )}
        </AuthenticatedLayout>
    );
}
