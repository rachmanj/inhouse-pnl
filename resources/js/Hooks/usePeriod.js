import { router, usePage } from '@inertiajs/react';
import { useCallback, useMemo } from 'react';

export function usePeriod() {
    const { period, periods = [] } = usePage().props;

    const currentPeriod = useMemo(() => {
        if (period) {
            return period;
        }
        return periods[0] ?? null;
    }, [period, periods]);

    const setPeriod = useCallback((periodId) => {
        router.get(
            window.location.pathname,
            { period: periodId },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, []);

    const periodLabel = useMemo(() => {
        if (!currentPeriod) {
            return '—';
        }
        const month = String(currentPeriod.month).padStart(2, '0');
        return `${currentPeriod.year}-${month}`;
    }, [currentPeriod]);

    return {
        period: currentPeriod,
        periods,
        periodLabel,
        setPeriod,
    };
}
