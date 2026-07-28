import { Head, router } from '@inertiajs/react';
import { Steps } from 'antd';
import { useState } from 'react';
import ConfirmStep from '@/Components/Import/ConfirmStep';
import MapColumnsStep from '@/Components/Import/MapColumnsStep';
import PreviewStep from '@/Components/Import/PreviewStep';
import UploadStep from '@/Components/Import/UploadStep';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const STEPS = ['Upload', 'Map Columns', 'Preview', 'Confirm'];

export default function Create({
    periods = [],
    sites = [],
    batch = null,
    columnMap = {},
    detectedColumns = [],
    stagingRows = [],
    summary = {},
    mappingCounts = {},
}) {
    const [current, setCurrent] = useState(batch ? 1 : 0);
    const [loading, setLoading] = useState(false);

    const goNext = () => setCurrent((c) => Math.min(c + 1, STEPS.length - 1));

    const handleUpload = (formData) => {
        setLoading(true);
        router.post(route('imports.store'), formData, {
            forceFormData: true,
            onFinish: () => setLoading(false),
            onSuccess: () => goNext(),
        });
    };

    const handleMapSubmit = (values) => {
        if (!batch?.id) {
            goNext();
            return;
        }
        setLoading(true);
        router.post(route('api.imports.preview', batch.id), values, {
            preserveState: true,
            onFinish: () => setLoading(false),
            onSuccess: () => goNext(),
        });
    };

    const handleConfirm = () => {
        if (!batch?.id) {
            return;
        }
        setLoading(true);
        router.post(route('imports.confirm', batch.id), {}, {
            onFinish: () => setLoading(false),
        });
    };

    return (
        <AuthenticatedLayout title="New Import">
            <Head title="New Import" />
            <Steps current={current} items={STEPS.map((title) => ({ title }))} style={{ marginBottom: 24 }} />

            {current === 0 && (
                <UploadStep
                    periods={periods}
                    sites={sites}
                    onSubmit={handleUpload}
                    loading={loading}
                />
            )}
            {current === 1 && (
                <MapColumnsStep
                    columnMap={columnMap}
                    detectedColumns={detectedColumns}
                    onSubmit={handleMapSubmit}
                    loading={loading}
                />
            )}
            {current === 2 && (
                <PreviewStep
                    summary={summary}
                    stagingRows={stagingRows}
                    mappingCounts={mappingCounts}
                    onSubmit={() => goNext()}
                    loading={loading}
                />
            )}
            {current === 3 && (
                <ConfirmStep
                    batch={batch}
                    summary={summary}
                    onConfirm={handleConfirm}
                    loading={loading}
                />
            )}
        </AuthenticatedLayout>
    );
}
