import { ProTable } from '@ant-design/pro-components';
import { formatCurrency } from '@/Utils/currency';

export default function JournalLineTable({ lines = [], editable = false, onChange }) {
    const columns = [
        {
            title: '#',
            dataIndex: 'line_order',
            width: 50,
            render: (_, __, index) => index + 1,
        },
        {
            title: 'Account',
            dataIndex: ['account', 'sap_code'],
            render: (_, record) => {
                if (record.account) {
                    return `${record.account.sap_code} — ${record.account.name}`;
                }
                return record.account_label ?? '—';
            },
        },
        {
            title: 'Memo',
            dataIndex: 'memo',
            ellipsis: true,
        },
        {
            title: 'Debit',
            dataIndex: 'debit',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
        {
            title: 'Credit',
            dataIndex: 'credit',
            align: 'right',
            render: (v) => formatCurrency(v),
        },
    ];

    if (editable && onChange) {
        columns.push({
            title: 'Actions',
            key: 'actions',
            valueType: 'option',
            render: (_, record, index) => [
                <a key="delete" onClick={() => onChange(lines.filter((_, i) => i !== index))}>
                    Remove
                </a>,
            ],
        });
    }

    const totalDebit = lines.reduce((sum, l) => sum + Number(l.debit || 0), 0);
    const totalCredit = lines.reduce((sum, l) => sum + Number(l.credit || 0), 0);

    return (
        <ProTable
            rowKey={(record, index) => record.id ?? index}
            search={false}
            options={false}
            pagination={false}
            columns={columns}
            dataSource={lines}
            size="small"
            summary={() => (
                <ProTable.Summary fixed>
                    <ProTable.Summary.Row>
                        <ProTable.Summary.Cell index={0} colSpan={3}>
                            <strong>Total</strong>
                        </ProTable.Summary.Cell>
                        <ProTable.Summary.Cell index={1} align="right">
                            <strong>{formatCurrency(totalDebit)}</strong>
                        </ProTable.Summary.Cell>
                        <ProTable.Summary.Cell index={2} align="right">
                            <strong>{formatCurrency(totalCredit)}</strong>
                        </ProTable.Summary.Cell>
                        {editable && <ProTable.Summary.Cell index={3} />}
                    </ProTable.Summary.Row>
                </ProTable.Summary>
            )}
        />
    );
}
