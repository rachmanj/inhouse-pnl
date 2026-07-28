import { Head, router } from '@inertiajs/react';
import { Button, Card, Col, Row, Tabs, Tag } from 'antd';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Studio({ package: pkg }) {
    const pending = pkg.approval_steps?.filter((s) => s.status === 'pending') ?? [];
    const approved = pkg.approval_steps?.filter((s) => s.status === 'approved') ?? [];

    return (
        <AuthenticatedLayout title={`Report Studio #${pkg.id}`}>
            <Head title="Report Studio" />
            <Row gutter={16} style={{ marginBottom: 16 }}>
                <Col><Button type="primary" onClick={() => router.post(`/reports/${pkg.id}/generate`)}>Generate</Button></Col>
                <Col><Button onClick={() => router.post(`/reports/${pkg.id}/deliver`, { recipients: ['finance@arkaledger.test'] })}>Deliver</Button></Col>
            </Row>
            <Tabs items={[
                { key: 'preview', label: 'Preview', children: <p>Sheet previews populate after generation. Artifacts: {pkg.artifacts?.length ?? 0}</p> },
                { key: 'approval', label: 'Approval Board', children: (
                    <Row gutter={16}>
                        <Col span={8}><Card title="Pending">{pending.map((s) => <div key={s.id}>{s.project_site?.code ?? 'Final'} <Tag>{s.approver_role}</Tag></div>)}</Card></Col>
                        <Col span={8}><Card title="Approved">{approved.map((s) => <div key={s.id}>{s.project_site?.code ?? 'Final'}</div>)}</Card></Col>
                    </Row>
                )},
            ]} />
        </AuthenticatedLayout>
    );
}
