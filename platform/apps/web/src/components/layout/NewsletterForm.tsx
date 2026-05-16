"use client";

export function NewsletterForm() {
    return (
        <form
            className="flex flex-col sm:flex-row gap-3 self-end w-full"
            onSubmit={(e) => e.preventDefault()}
            aria-label="Newsletter signup"
        >
            <label className="sr-only" htmlFor="newsletter-email">
                Email address
            </label>
            <input
                id="newsletter-email"
                type="email"
                required
                placeholder="you@example.com"
                className="flex-1 rounded-full bg-cream-100/10 border-cream-100/10 text-cream-100
                           placeholder:text-cream-200/50 px-5 py-3.5
                           focus:border-amber-400 focus:ring-2 focus:ring-amber-400/40 focus:bg-cream-100/15"
            />
            <button type="submit" className="btn-accent btn-md">
                Sign me up
            </button>
        </form>
    );
}
