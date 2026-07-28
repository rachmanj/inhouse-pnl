import { Calendar, Badge, List, Typography } from 'antd';
import dayjs from 'dayjs';
import FilingStatusBadge from '@/Components/Tax/FilingStatusBadge';
import { formatCurrency } from '@/Utils/currency';

export default function DueDateRadar({ filings = [] }) {
    const byDate = filings.reduce((acc, filing) => {
        const key = dayjs(filing.due_date).format('YYYY-MM-DD');
        if (!acc[key]) {
            acc[key] = [];
        }
        acc[key].push(filing);
        return acc;
    }, {});

    const dateCellRender = (value) => {
        const key = value.format('YYYY-MM-DD');
        const items = byDate[key] ?? [];
        if (!items.length) {
            return null;
        }
        return (
            <ul style={{ listStyle: 'none', padding: 0, margin: 0, fontSize: 11 }}>
                {items.slice(0, 3).map((item) => (
                    <li key={item.id}>
                        <Badge
                            status={item.status === 'filed' ? 'success' : item.status === 'late' ? 'error' : 'warning'}
                            text={`${item.tax_type?.toUpperCase()} ${item.site?.code ?? ''}`}
                        />
                    </li>
                ))}
                {items.length > 3 && <li>+{items.length - 3} more</li>}
            </ul>
        );
    };

    const upcoming = [...filings]
        .filter((f) => f.status !== 'filed')
        .sort((a, b) => dayjs(a.due_date).unix() - dayjs(b.due_date).unix())
        .slice(0, 10);

    return (
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 320px', gap: 24 }}>
            <Calendar cellRender={dateCellRender} />
            <div>
                <Typography.Title level={5}>Upcoming Due Dates</Typography.Title>
                <List
                    size="small"
                    dataSource={upcoming}
                    locale={{ emptyText: 'No upcoming filings' }}
                    renderItem={(item) => (
                        <List.Item
                            actions={[<FilingStatusBadge key="s" status={item.status} dueDate={item.due_date} />]}
                        >
                            <List.Item.Meta
                                title={`${item.tax_type?.toUpperCase()} — ${item.site?.code ?? 'Entity'}`}
                                description={`Due ${dayjs(item.due_date).format('DD MMM YYYY')} · ${formatCurrency(item.amount_reported)}`}
                            />
                        </List.Item>
                    )}
                />
            </div>
        </div>
    );
}
