/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: true,
    transpilePackages: ["@baf/shared"],
    async rewrites() {
        // Proxy API calls to the Fastify search-api so the browser never
        // needs to know about port 4000.
        const api = process.env.NEXT_PUBLIC_API_BASE ?? "http://localhost:4050";
        return [
            { source: "/api/:path*", destination: `${api}/:path*` },
            { source: "/go/:clickId", destination: `${api}/go/:clickId` },
        ];
    },
};

export default nextConfig;
