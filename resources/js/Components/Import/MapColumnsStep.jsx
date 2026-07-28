import { ProForm, ProFormSelect } from '@ant-design/pro-components';
import { Alert, Col, Row, Typography } from 'antd';

const COLUMN_FIELDS = [
    { key: 'account_code', label: 'Account Code' },
    { key: 'account_name', label: 'Account Name' },
    { key: 'debit', label: 'Debit' },
    { key: 'credit', label: 'Credit' },
    { key: 'balance', label: 'Balance' },
];

export default function MapColumnsStep({ columnMap = {}, detectedColumns = [], onSubmit, loading = false }) {
    const columnOptions = detectedColumns.map((col) => ({
        value: col.letter ?? col,
        label: col.label ? `${col.letter ?? col} — ${col.label}` : col.letter ?? col,
    }));

    return (
        <>
            <Alert
                type="info"
                showIcon
                style={{ marginBottom: 16 }}
                message="Confirm or correct the column mapping. Learned mappings are pre-filled when available."
            />
            <ProForm
                initialValues={columnMap}
                submitter={{
                    searchConfig: { submitText: 'Apply Mapping' },
                    submitButtonProps: { loading },
                }}
                onFinish={onSubmit}
            >
                <Row gutter={[16, 8]}>
                    {COLUMN_FIELDS.map((field) => (
                        <Col key={field.key} xs={24} sm={12}>
                            <ProFormSelect
                                name={field.key}
                                label={field.label}
                                rules={[{ required: ['account_code', 'debit', 'credit'].includes(field.key), message: 'Required' }]}
                                options={columnOptions}
                                placeholder="Select column"
                                allowClear={!['account_code', 'debit', 'credit'].includes(field.key)}
                            />
                        </Col>
                    ))}
                </Row>
            </ProForm>
            {Object.keys(columnMap).length > 0 && (
                <Typography.Text type="secondary" style={{ display: 'block', marginTop: 8 }}>
                    Detected mapping applied from parser / learned cache.
                </Typography.Text>
            )}
        </>
    );
}
