import { Tag } from 'antd';

const STATUS_COLORS = {
    pending: 'default',
    staged: 'blue',
    mapped: 'cyan',
    validated: 'gold',
    completed: 'green',
    failed: 'red',
    open: 'blue',
    in_review: 'gold',
    approved: 'green',
    delivered: 'purple',
    locked: 'default',
    draft: 'default',
    pending_approval: 'gold',
    rejected: 'red',
    reconciled: 'green',
    filed: 'green',
    late: 'red',
};

export default function StatusChip({ status, label }) {
    const color = STATUS_COLORS[status] ?? 'default';
    const text = label ?? status?.replace(/_/g, ' ') ?? '—';

    return <Tag color={color}>{text}</Tag>;
}
