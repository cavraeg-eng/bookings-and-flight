import { PrismaClient } from "@prisma/client";

/**
 * Singleton PrismaClient — reused across the process lifetime.
 * In dev with hot-reload (tsx watch) a global ref prevents leaked connections.
 */

const globalForPrisma = globalThis as unknown as { __prisma?: PrismaClient };

export const prisma: PrismaClient =
    globalForPrisma.__prisma ??
    new PrismaClient({
        log:
            process.env.NODE_ENV === "production"
                ? ["error"]
                : ["query", "error", "warn"],
    });

if (process.env.NODE_ENV !== "production") {
    globalForPrisma.__prisma = prisma;
}

/**
 * Call once on SIGINT / SIGTERM to close the connection pool cleanly.
 */
export async function disconnectDb(): Promise<void> {
    await prisma.$disconnect();
}
