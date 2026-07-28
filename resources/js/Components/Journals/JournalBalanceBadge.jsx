import { CheckCircleOutlined, CloseCircleOutlined } from '@ant-design/icons';
import { Tag } from 'antd';
import { formatCurrency } from '@/Utils/currency';

export default function JournalBalanceBadge({ totalDebit = 0, totalCredit = 0 }) {
    const balanced = Math.abs(Number(totalDebit) - Number(totalCredit)) < 0.01;

    if (balanced) {
        return (
            <Tag icon={<CheckCircleOutlined />} color="success">
                Balanced ({formatCurrency(totalDebit)})
            </Tag>
        );
    }

    const diff = Number(totalDebit) - Number(totalCredit);
    return (
        <Tag icon={<CloseCircleOutlined />} color="error">
            Unbalanced: {formatCurrency(diff)} diff
        </Tag>
    );
}
