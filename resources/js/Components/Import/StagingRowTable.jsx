import { ProTable } from '@ant-design/pro-components';
import { Typography } from 'antd';
import StatusChip from '@/Components/Shared/StatusChip';
import { formatCurrency } from '@/Utils/currency';

export default function StagingRowTable({
    rows = [],
    accounts = [],
    onAssignAccount,
    pagination = { pageSize: 20 },
    toolBarRender,
    ...rest
}) {
    const columns = [
        { title: '#', dataIndex: 'row_number', width: 60 },
        { title: 'Account Code', dataIndex: 'raw_account_code', width: 120 },
        { title: 'Account Name', dataIndex: 'raw_account_name', ellipsis: true },
        {
            title: 'Debit',
            dataIndex: 'raw_debit',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Credit',
            dataIndex: 'raw_credit',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Balance',
            dataIndex: 'raw_balance',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Mapped Account',
            dataIndex: ['mapped_account', 'sap_code'],
            render: (_, record) => {
                if (record.mapped_account) {
                    return `${record.mapped_account.sap_code} — ${record.mapped_account.name}`;
                }
                return <Typography.Text type="secondary">—</Typography.Text>;
            },
        },
        {
            title: 'Status',
            dataIndex: 'mapping_status',
            render: (status) => <StatusChip status={status === 'mapped' ? 'completed' : status} />,
        },
        {
            title: 'Error',
            dataIndex: 'error_message',
            ellipsis: true,
            render: (msg) => msg ? <Typography.Text type="danger">{msg}</Typography.Text> : null,
        },
    ];

    if (onAssignAccount && accounts.length) {
        columns.push({
            title: 'Assign',
            key: 'assign',
            width: 200,
            render: (_, record) => (
                <select
                    defaultValue=""
                    onChange={(e) => {
                        if (e.target.value) {
                            onAssignAccount(record.id, Number(e.target.value));
                        }
                    }}
                    style={{ width: '100%' }}
                >
                    <option value="">Select account…</option>
                    {accounts.map((a) => (
                        <option key={a.id} value={a.id}>
                            {a.sap_code} — {a.name}
                        </option>
                    ))}
                </select>
            ),
        });
    }

    return (
        <ProTable
            rowKey="id"
            search={false}
            options={false}
            columns={columns}
            dataSource={rows}
            pagination={pagination}
            scroll={{ x: 1100 }}
            toolBarRender={toolBarRender}
            size="small"
            {...rest}
        />
    );
}
