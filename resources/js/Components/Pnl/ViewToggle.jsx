import { Segmented } from 'antd';

export default function ViewToggle({ value = 'pnl', onChange }) {
    return (
        <Segmented
            value={value}
            onChange={onChange}
            options={[
                { label: 'P&L', value: 'pnl' },
                { label: 'Rincian', value: 'rincian' },
            ]}
        />
    );
}
