"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { Menu, X, Plane } from "lucide-react";
import { cn } from "@/lib/cn";

const NAV = [
    { href: "/flights", label: "Flights" },
    { href: "/hotels", label: "Hotels" },
    { href: "/cars", label: "Cars" },
    { href: "/activities", label: "Activities" },
    { href: "/guides", label: "Guides" },
    { href: "/integrations", label: "Integrations" },
];

export function SiteHeader() {
    const pathname = usePathname();
    const [scrolled, setScrolled] = useState(false);
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 8);
        onScroll();
        window.addEventListener("scroll", onScroll, { passive: true });
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    // Close mobile nav on route change
    useEffect(() => setOpen(false), [pathname]);

    // Lock body scroll when drawer open
    useEffect(() => {
        document.body.style.overflow = open ? "hidden" : "";
        return () => {
            document.body.style.overflow = "";
        };
    }, [open]);

    return (
        <header
            className={cn(
                "sticky top-0 z-40 transition-all duration-300",
                scrolled
                    ? "bg-cream-100/85 backdrop-blur-md border-b border-ink-900/10 shadow-subtle"
                    : "bg-transparent border-b border-transparent",
            )}
        >
            <div className="container flex items-center justify-between h-16 sm:h-20">
                {/* Brand */}
                <Link
                    href="/"
                    className="group flex items-center gap-2.5 text-ink-900"
                    aria-label="Bookings and Flights, home"
                >
                    <span
                        className="flex h-9 w-9 items-center justify-center rounded-full bg-ink-900 text-amber-400
                                   transition-transform duration-300 group-hover:-rotate-12"
                    >
                        <Plane className="h-4 w-4" strokeWidth={2.5} />
                    </span>
                    <span className="font-display text-xl leading-none tracking-tight">
                        Bookings<span className="text-amber-500">&</span>Flights
                    </span>
                </Link>

                {/* Desktop nav */}
                <nav
                    className="hidden md:flex items-center gap-1"
                    aria-label="Primary"
                >
                    {NAV.map((item) => {
                        const active = pathname === item.href;
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    "relative px-4 py-2 rounded-full text-sm font-medium transition-colors",
                                    active
                                        ? "text-ink-900 bg-ink-900/[0.06]"
                                        : "text-ink-500 hover:text-ink-900 hover:bg-ink-900/[0.04]",
                                )}
                            >
                                {item.label}
                                {active && (
                                    <span className="absolute left-1/2 -translate-x-1/2 -bottom-0.5 h-0.5 w-6 rounded-full bg-amber-500" />
                                )}
                            </Link>
                        );
                    })}
                </nav>

                {/* Desktop CTAs */}
                <div className="hidden md:flex items-center gap-2">
                    <Link
                        href="#"
                        className="text-sm font-medium text-ink-500 hover:text-ink-900 px-3 py-2"
                    >
                        Sign in
                    </Link>
                    <Link href="/flights" className="btn-accent btn-sm">
                        Start searching
                    </Link>
                </div>

                {/* Mobile menu toggle */}
                <button
                    type="button"
                    className="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full text-ink-900 hover:bg-ink-900/5"
                    aria-label={open ? "Close menu" : "Open menu"}
                    aria-expanded={open}
                    aria-controls="mobile-nav"
                    onClick={() => setOpen((o) => !o)}
                >
                    {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
                </button>
            </div>

            {/* Mobile drawer */}
            <div
                id="mobile-nav"
                className={cn(
                    "md:hidden fixed inset-x-0 top-16 sm:top-20 bottom-0 z-30 bg-cream-100",
                    "transition-all duration-300 origin-top",
                    open
                        ? "opacity-100 translate-y-0 pointer-events-auto"
                        : "opacity-0 -translate-y-2 pointer-events-none",
                )}
                aria-hidden={!open}
            >
                <nav className="container py-8 flex flex-col gap-1" aria-label="Mobile">
                    {NAV.map((item) => {
                        const active = pathname === item.href;
                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                className={cn(
                                    "flex items-center justify-between px-4 py-4 rounded-2xl text-lg font-display",
                                    active
                                        ? "bg-ink-900 text-cream-100"
                                        : "text-ink-900 hover:bg-ink-900/5",
                                )}
                            >
                                {item.label}
                                <span
                                    className={cn(
                                        "text-xl",
                                        active ? "text-amber-400" : "text-ink-300",
                                    )}
                                >
                                    →
                                </span>
                            </Link>
                        );
                    })}
                    <hr className="my-4 border-ink-900/10" />
                    <Link href="#" className="btn-ghost btn-md w-full">
                        Sign in
                    </Link>
                    <Link href="/flights" className="btn-accent btn-md w-full mt-2">
                        Start searching
                    </Link>
                </nav>
            </div>
        </header>
    );
}
