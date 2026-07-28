import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard({ title = 'Dashboard' }) {
    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />
            <p>Welcome to ArkaLedger — FinSight P&L Dashboard</p>
        </AuthenticatedLayout>
    );
}
