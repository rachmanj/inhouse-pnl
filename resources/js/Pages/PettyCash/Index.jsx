import { Head, Link } from '@inertiajs/react';
import { Card, Col, Row, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { formatCurrency } from '@/Utils/currency';

export default function Index({ funds }) {
    return (
        <AuthenticatedLayout title="Petty Cash">
            <Head title="Petty Cash" />
            <Row gutter={[16, 16]}>
                {funds.map((fund) => (
                    <Col key={fund.id} xs={24} sm={12} lg={8}>
                        <Card title={fund.project_site?.code} extra={<Tag>{fund.status}</Tag>}>
                            <p>Opening: {formatCurrency(fund.opening_balance)}</p>
                            <p>Closing: {formatCurrency(fund.closing_balance)}</p>
                            <Link href={`/petty-cash/${fund.id}/expenses`}>View Expenses</Link>
                        </Card>
                    </Col>
                ))}
            </Row>
        </AuthenticatedLayout>
    );
}
