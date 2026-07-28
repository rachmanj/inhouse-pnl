import { ProCard, ProDescriptions } from '@ant-design/pro-components';
import { Button, Col, InputNumber, Row, Typography } from 'antd';
import { useState } from 'react';
import StagingRowTable from '@/Components/Import/StagingRowTable';
import StatusChip from '@/Components/Shared/StatusChip';
import { formatCurrency } from '@/Utils/currency';

export default function PreviewStep({
    summary = {},
    stagingRows = [],
    mappingCounts = {},
    onControlTotalChange,
    onSubmit,
    loading = false,
}) {
    const [controlTotal, setControlTotal] = useState(summary.sap_control_total ?? null);

    const handleControlChange = (value) => {
        setControlTotal(value);
        onControlTotalChange?.(value);
    };

    return (
        <>
            <Row gutter={[16, 16]} style={{ marginBottom: 16 }}>
                <Col xs={24} md={8}>
                    <ProCard bordered size="small" title="Debit Total">
                        <Typography.Text>{formatCurrency(summary.total_debit ?? 0)}</Typography.Text>
                    </ProCard>
                </Col>
                <Col xs={24} md={8}>
                    <ProCard bordered size="small" title="Credit Total">
                        <Typography.Text>{formatCurrency(summary.total_credit ?? 0)}</Typography.Text>
                    </ProCard>
                </Col>
                <Col xs={24} md={8}>
                    <ProCard bordered size="small" title="SAP Control Total">
                        <InputNumber
                            style={{ width: '100%' }}
                            value={controlTotal}
                            onChange={handleControlChange}
                            formatter={(v) => `${v}`.replace(/\B(?=(\d{3})+(?!\d))/g, '.')}
                            parser={(v) => v.replace(/\./g, '')}
                            placeholder="Paste SAP control total"
                        />
                    </ProCard>
                </Col>
            </Row>

            <ProDescriptions
                column={4}
                size="small"
                style={{ marginBottom: 16 }}
                dataSource={mappingCounts}
                columns={[
                    { title: 'Mapped', dataIndex: 'mapped', render: (v) => <StatusChip status="completed" label={v ?? 0} /> },
                    { title: 'Unmapped', dataIndex: 'unmapped', render: (v) => <StatusChip status="pending" label={v ?? 0} /> },
                    { title: 'Ambiguous', dataIndex: 'ambiguous', render: (v) => <StatusChip status="validated" label={v ?? 0} /> },
                    { title: 'Errors', dataIndex: 'error', render: (v) => <StatusChip status="failed" label={v ?? 0} /> },
                ]}
            />

            <StagingRowTable
                rows={stagingRows}
                pagination={{ pageSize: 10 }}
                toolBarRender={() => [
                    <Typography.Text key="hint" type="secondary">
                        Review staging rows before confirming import
                    </Typography.Text>,
                ]}
            />

            {onSubmit && (
                <div style={{ marginTop: 16, textAlign: 'right' }}>
                    <Button type="primary" loading={loading} onClick={() => onSubmit({ sap_control_total: controlTotal })}>
                        Continue to Confirm
                    </Button>
                </div>
            )}
        </>
    );
}
