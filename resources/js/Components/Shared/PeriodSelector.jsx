import { Select, Space, Typography } from 'antd';
import { usePeriod } from '@/Hooks/usePeriod';

export default function PeriodSelector({ style }) {
    const { period, periods, periodLabel, setPeriod } = usePeriod();

    if (!periods.length) {
        return <Typography.Text type="secondary">No periods available</Typography.Text>;
    }

    return (
        <Space style={style}>
            <Typography.Text type="secondary">Period:</Typography.Text>
            <Select
                value={period?.id}
                style={{ minWidth: 140 }}
                onChange={setPeriod}
                options={periods.map((p) => ({
                    value: p.id,
                    label: `${p.year}-${String(p.month).padStart(2, '0')}`,
                }))}
                placeholder={periodLabel}
            />
        </Space>
    );
}
