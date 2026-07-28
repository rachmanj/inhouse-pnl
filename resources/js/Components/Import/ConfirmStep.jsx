import { CheckCircleOutlined, WarningOutlined } from '@ant-design/icons';
import { ProCard, ProDescriptions } from '@ant-design/pro-components';
import { Alert, Button, Space, Typography } from 'antd';
import { formatCurrency } from '@/Utils/currency';
import StatusChip from '@/Components/Shared/StatusChip';

export default function ConfirmStep({ batch = {}, summary = {}, onConfirm, loading = false }) {
    const reconciled = summary.is_reconciled ?? summary.discrepancy === 0;

    return (
        <>
            <Alert
                type={reconciled ? 'success' : 'warning'}
                showIcon
                icon={reconciled ? <CheckCircleOutlined /> : <WarningOutlined />}
                style={{ marginBottom: 16 }}
                message={reconciled ? 'Reconciliation passed' : 'Reconciliation discrepancy detected'}
                description={
                    reconciled
                        ? 'Staged totals match the SAP control total. Confirm to upsert account balances.'
                        : `Discrepancy: ${formatCurrency(summary.discrepancy ?? 0)}`
                }
            />

            <ProCard bordered title="Import Summary">
                <ProDescriptions
                    column={2}
                    dataSource={{
                        filename: batch.original_filename,
                        site: batch.site?.code ?? batch.project_site?.code,
                        period: batch.period
                            ? `${batch.period.year}-${String(batch.period.month).padStart(2, '0')}`
                            : '—',
                        status: batch.status,
                        total_rows: batch.total_rows ?? batch.staged_rows,
                        total_debit: formatCurrency(summary.total_debit ?? 0),
                        total_credit: formatCurrency(summary.total_credit ?? 0),
                    }}
                    columns={[
                        { title: 'File', dataIndex: 'filename' },
                        { title: 'Site', dataIndex: 'site' },
                        { title: 'Period', dataIndex: 'period' },
                        {
                            title: 'Status',
                            dataIndex: 'status',
                            render: (v) => <StatusChip status={v} />,
                        },
                        { title: 'Rows', dataIndex: 'total_rows' },
                        { title: 'Total Debit', dataIndex: 'total_debit' },
                        { title: 'Total Credit', dataIndex: 'total_credit' },
                    ]}
                />
            </ProCard>

            <Typography.Paragraph type="secondary" style={{ marginTop: 16 }}>
                Confirming will upsert account balances and trigger P&L recalculation for this site and period.
            </Typography.Paragraph>

            <Space style={{ marginTop: 16 }}>
                <Button type="primary" size="large" loading={loading} onClick={onConfirm}>
                    Confirm Import
                </Button>
            </Space>
        </>
    );
}
