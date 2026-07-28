import { Tag } from 'antd';
import dayjs from 'dayjs';

const STATUS_COLORS = {
    pending: 'default',
    filed: 'green',
    late: 'red',
};

export default function FilingStatusBadge({ status, dueDate }) {
    const isOverdue = status === 'pending' && dueDate && dayjs(dueDate).isBefore(dayjs(), 'day');
    const displayStatus = isOverdue ? 'late' : status;
    const color = STATUS_COLORS[displayStatus] ?? 'default';

    return (
        <Tag color={color}>
            {displayStatus?.replace(/_/g, ' ')}
        </Tag>
    );
}
