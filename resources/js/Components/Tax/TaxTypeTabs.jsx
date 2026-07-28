import { Tabs } from 'antd';

const TAX_TYPES = [
    { key: 'ppn', label: 'PPN' },
    { key: 'pph21', label: 'PPh 21' },
    { key: 'pph23', label: 'PPh 23' },
    { key: 'pph25', label: 'PPh 25' },
    { key: 'pph4a2', label: 'PPh 4(2)' },
];

export default function TaxTypeTabs({ activeKey = 'ppn', onChange, children }) {
    return (
        <Tabs
            activeKey={activeKey}
            onChange={onChange}
            items={TAX_TYPES.map((type) => ({
                key: type.key,
                label: type.label,
                children: typeof children === 'function' ? children(type.key) : children,
            }))}
        />
    );
}

export { TAX_TYPES };
