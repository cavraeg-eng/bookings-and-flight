/**
 * In-process TTL-based LRU cache.
 *
 * Uses a plain Map (insertion-ordered) with per-entry expiry timestamps.
 * When max entries is reached the oldest entry is evicted.
 * No external dependencies required.
 */

interface CacheEntry<T = unknown> {
    value: T;
    expiresAt: number; // Date.now() + ttlMs
}

const MAX_ENTRIES = 1_000;

const store = new Map<string, CacheEntry>();

let hits = 0;
let misses = 0;

// ── public API ───────────────────────────────────────────────

/** Retrieve a cached value. Returns `undefined` on miss or expiry. */
export function get<T>(key: string): T | undefined {
    const entry = store.get(key);
    if (!entry) {
        misses++;
        return undefined;
    }
    if (Date.now() > entry.expiresAt) {
        store.delete(key);
        misses++;
        return undefined;
    }
    // Move to end so it's treated as "recently used"
    store.delete(key);
    store.set(key, entry);
    hits++;
    return entry.value as T;
}

/** Store a value with a per-entry TTL (milliseconds). */
export function set<T>(key: string, value: T, ttlMs: number): void {
    // Delete first so re-insertion moves it to the end
    store.delete(key);

    // Evict oldest entry if at capacity
    if (store.size >= MAX_ENTRIES) {
        const oldest = store.keys().next().value as string;
        store.delete(oldest);
    }

    store.set(key, { value, expiresAt: Date.now() + ttlMs });
}

/** Check whether a non-expired entry exists for `key`. */
export function has(key: string): boolean {
    const entry = store.get(key);
    if (!entry) return false;
    if (Date.now() > entry.expiresAt) {
        store.delete(key);
        return false;
    }
    return true;
}

/** Remove a single key. */
export function del(key: string): void {
    store.delete(key);
}

/** Flush every entry and reset hit/miss counters. */
export function clear(): void {
    store.clear();
    hits = 0;
    misses = 0;
}

/** Snapshot of cache performance counters. */
export function stats(): {
    hits: number;
    misses: number;
    size: number;
    maxSize: number;
} {
    return { hits, misses, size: store.size, maxSize: MAX_ENTRIES };
}

/**
 * Build a deterministic cache key from an ordered list of parts.
 *
 * Usage:
 *   generateKey("flights", origin, dest, depart, ret ?? "", currency)
 */
export function generateKey(...parts: string[]): string {
    return parts.join("::");
}
