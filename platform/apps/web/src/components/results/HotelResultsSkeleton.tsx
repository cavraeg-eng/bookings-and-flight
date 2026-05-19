import { Skeleton } from "@/components/ui/Skeleton";

/* ------------------------------------------------------------------ */
/*  Skeleton card — matches HotelCard layout                           */
/* ------------------------------------------------------------------ */

function SkeletonCard() {
    return (
        <div className="rounded-2xl border border-cream-200/60 bg-white shadow-card overflow-hidden">
            {/* Image placeholder */}
            <Skeleton variant="custom" className="aspect-[16/10] w-full rounded-none" />

            {/* Content */}
            <div className="p-5 space-y-3">
                {/* Stars */}
                <div className="flex gap-1">
                    {Array.from({ length: 5 }, (_, i) => (
                        <Skeleton key={i} variant="custom" className="h-3.5 w-3.5 rounded" />
                    ))}
                </div>

                {/* Title */}
                <Skeleton variant="text" className="h-5 w-3/4" />

                {/* Location */}
                <Skeleton variant="text" className="h-4 w-1/2" />

                {/* Amenities */}
                <div className="flex gap-2 pt-1">
                    <Skeleton variant="custom" className="h-7 w-16 rounded-full" />
                    <Skeleton variant="custom" className="h-7 w-20 rounded-full" />
                    <Skeleton variant="custom" className="h-7 w-14 rounded-full" />
                </div>

                {/* Price + button */}
                <div className="flex items-end justify-between pt-3 border-t border-cream-200/60">
                    <div className="space-y-1">
                        <Skeleton variant="text" className="h-7 w-20" />
                        <Skeleton variant="text" className="h-3 w-14" />
                    </div>
                    <Skeleton variant="custom" className="h-8 w-24 rounded-lg" />
                </div>
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  HotelResultsSkeleton — 4 skeleton cards                            */
/* ------------------------------------------------------------------ */

export function HotelResultsSkeleton() {
    return (
        <div
            className="grid grid-cols-1 md:grid-cols-2 gap-5"
            role="status"
            aria-label="Loading hotel results"
        >
            {Array.from({ length: 4 }, (_, i) => (
                <SkeletonCard key={i} />
            ))}
            <span className="sr-only">Loading hotel results…</span>
        </div>
    );
}
