import { Card } from "@/components/ui/Card";
import { Skeleton } from "@/components/ui/Skeleton";
import { cn } from "@/lib/cn";

/* ------------------------------------------------------------------ */
/*  Props                                                              */
/* ------------------------------------------------------------------ */

export interface FlightResultsSkeletonProps {
    count?: number;
    className?: string;
}

/* ------------------------------------------------------------------ */
/*  Single skeleton card                                               */
/* ------------------------------------------------------------------ */

function SkeletonCard() {
    return (
        <Card padding="none" className="overflow-hidden">
            <div className="flex flex-col sm:flex-row sm:items-center gap-4 p-4 sm:p-5">
                {/* Left — Airline */}
                <div className="flex items-center gap-3 sm:w-[160px] shrink-0">
                    <Skeleton variant="custom" width={40} height={40} className="rounded-xl" />
                    <div className="flex-1 space-y-2">
                        <Skeleton variant="custom" width={90} height={14} className="rounded" />
                        <Skeleton variant="custom" width={60} height={10} className="rounded" />
                    </div>
                </div>

                {/* Center — Times */}
                <div className="flex-1 flex items-center gap-4">
                    <div className="space-y-1.5">
                        <Skeleton variant="custom" width={48} height={20} className="rounded" />
                        <Skeleton variant="custom" width={36} height={8} className="rounded" />
                    </div>
                    <div className="flex-1 flex flex-col items-center gap-1.5">
                        <Skeleton variant="custom" width="100%" height={2} className="rounded" />
                        <Skeleton variant="custom" width={60} height={10} className="rounded" />
                        <Skeleton variant="custom" width={50} height={16} className="rounded-pill" />
                    </div>
                    <div className="space-y-1.5">
                        <Skeleton variant="custom" width={48} height={20} className="rounded" />
                        <Skeleton variant="custom" width={36} height={8} className="rounded" />
                    </div>
                </div>

                {/* Right — Price & button */}
                <div className="flex items-center gap-4 sm:flex-col sm:items-end sm:gap-2 sm:w-[140px] shrink-0">
                    <Skeleton variant="custom" width={80} height={28} className="rounded" />
                    <Skeleton variant="custom" width={100} height={32} className="rounded-lg" />
                </div>
            </div>
        </Card>
    );
}

/* ------------------------------------------------------------------ */
/*  Component                                                          */
/* ------------------------------------------------------------------ */

export function FlightResultsSkeleton({ count = 5, className }: FlightResultsSkeletonProps) {
    return (
        <div className={cn("space-y-4", className)} role="status" aria-label="Loading flights">
            {Array.from({ length: count }).map((_, i) => (
                <SkeletonCard key={i} />
            ))}
            <span className="sr-only">Loading flight results…</span>
        </div>
    );
}
