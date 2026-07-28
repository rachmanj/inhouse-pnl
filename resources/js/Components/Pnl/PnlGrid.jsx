import { ProTable } from '@ant-design/pro-components';
import { Typography } from 'antd';
import VarianceCell from '@/Components/Pnl/VarianceCell';
import { formatCurrency } from '@/Utils/currency';

const MONTHS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
const MONTH_LABELS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function buildYearColumns(year, yearKey, label) {
    const monthCols = MONTHS.map((m, i) => ({
        title: MONTH_LABELS[i],
        dataIndex: [yearKey, 'months', m],
        align: 'right',
        width: 110,
        render: (value, record) => (
            <VarianceCell
                value={value}
                variance={record.variance?.[yearKey]?.months?.[m]}
            />
        ),
    }));

    return {
        title: `${year} (${label})`,
        children: [
            ...monthCols,
            {
                title: 'TOTAL',
                dataIndex: [yearKey, 'total'],
                align: 'right',
                width: 120,
                render: (value, record) => (
                    <VarianceCell value={value} variance={record.variance?.[yearKey]?.total} />
                ),
            },
            {
                title: 'AVG',
                dataIndex: [yearKey, 'avg'],
                align: 'right',
                width: 110,
                render: (value) => formatCurrency(value),
            },
            {
                title: '%',
                dataIndex: [yearKey, 'percent_of_revenue'],
                align: 'right',
                width: 70,
                render: (value) => (value != null ? `${Number(value).toFixed(1)}%` : '—'),
            },
        ],
    };
}

function attachChildren(rows) {
    return rows.map((row) => ({
        ...row,
        children: row.children?.length ? attachChildren(row.children) : undefined,
    }));
}

export default function PnlGrid({
    rows = [],
    baselineYear = 2024,
    currentYear = new Date().getFullYear(),
    loading = false,
    scroll = { x: 2800 },
}) {
    const columns = [
        {
            title: 'Account / Line',
            dataIndex: 'name',
            fixed: 'left',
            width: 260,
            render: (name, record) => (
                <Typography.Text strong={record.is_subtotal}>
                    {record.code ? `${record.code} — ` : ''}
                    {name}
                </Typography.Text>
            ),
        },
        buildYearColumns(baselineYear, 'baseline', 'baseline'),
        buildYearColumns(currentYear, 'current', 'current'),
    ];

    const dataSource = attachChildren(rows);

    return (
        <ProTable
            rowKey="id"
            search={false}
            options={false}
            pagination={false}
            loading={loading}
            columns={columns}
            dataSource={dataSource}
            scroll={scroll}
            expandable={{
                defaultExpandAllRows: false,
                indentSize: 16,
            }}
            size="small"
            bordered
        />
    );
}
