"use client";

import { useState, useEffect, useCallback } from "react";
import { useRouter } from "next/navigation";
import {
    Plane,
    Hotel,
    Car,
    Compass,
    ArrowRightLeft,
    Clock,
    X,
} from "lucide-react";
import { cn } from "@/lib/cn";
import { inDays } from "@/lib/search";
import { AutocompleteInput, type AirportSuggestion } from "./AutocompleteInput";
import { DatePicker } from "./DatePicker";
import { PassengerSelector, type PassengerCounts } from "./PassengerSelector";
import { CabinSelector, type CabinClass } from "./CabinSelector";
import { GuestSelector, SearchButton } from "./SearchFormControls";

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

export type Vertical = "flights" | "hotels" | "cars" | "activities";

export interface SearchFormProps {
    initialTab?: Vertical;
    initialValues?: Partial<Record<string, string>>;
    compact?: boolean;
    className?: string;
}

interface RecentSearch {
    tab: Vertical;
    label: string;
    url: string;
    ts: number;
}

/* ------------------------------------------------------------------ */
/*  Tab config                                                         */
/* ------------------------------------------------------------------ */

const TABS: { id: Vertical; label: string; Icon: typeof Plane }[] = [
    { id: "flights", label: "Flights", Icon: Plane },
    { id: "hotels", label: "Hotels", Icon: Hotel },
    { id: "cars", label: "Cars", Icon: Car },
    { id: "activities", label: "Activities", Icon: Compass },
];

/* ------------------------------------------------------------------ */
/*  Recent searches helpers                                            */
/* ------------------------------------------------------------------ */

const RECENT_KEY = "bf-recent-searches";
const MAX_RECENT = 5;

function loadRecent(): RecentSearch[] {
    if (typeof window === "undefined") return [];
    try {
        return JSON.parse(localStorage.getItem(RECENT_KEY) ?? "[]");
    } catch {
        return [];
    }
}

function saveRecent(search: RecentSearch) {
    const list = loadRecent().filter((r) => r.url !== search.url);
    list.unshift(search);
    localStorage.setItem(RECENT_KEY, JSON.stringify(list.slice(0, MAX_RECENT)));
}

/* ------------------------------------------------------------------ */
/*  Main SearchForm                                                    */
/* ------------------------------------------------------------------ */

export function SearchForm({
    initialTab = "flights",
    initialValues = {},
    compact = false,
    className,
}: SearchFormProps) {
    const [tab, setTab] = useState<Vertical>(initialTab);
    const router = useRouter();
    const [recentSearches, setRecentSearches] = useState<RecentSearch[]>([]);

    // Load recent searches on mount
    useEffect(() => {
        setRecentSearches(loadRecent());
    }, []);

    /* ---- Flights state ---- */
    const [flightOrigin, setFlightOrigin] = useState<AirportSuggestion | null>(
        initialValues.origin ? { code: initialValues.origin, name: "", city: "", country: "" } : null,
    );
    const [flightDest, setFlightDest] = useState<AirportSuggestion | null>(
        initialValues.destination ? { code: initialValues.destination, name: "", city: "", country: "" } : null,
    );
    const [flightDepart, setFlightDepart] = useState(initialValues.depart ?? inDays(14));
    const [flightReturn, setFlightReturn] = useState(initialValues.return ?? "");
    const [tripType, setTripType] = useState<"roundtrip" | "oneway">(initialValues.return ? "roundtrip" : "oneway");
    const [passengers, setPassengers] = useState<PassengerCounts>({
        adults: Number(initialValues.adults ?? "1"),
        children: Number(initialValues.children ?? "0"),
        infants: Number(initialValues.infants ?? "0"),
    });
    const [cabin, setCabin] = useState<CabinClass>((initialValues.cabin as CabinClass) ?? "economy");

    /* ---- Hotels state ---- */
    const [hotelDest, setHotelDest] = useState<AirportSuggestion | null>(
        initialValues.destination ? { code: initialValues.destination, name: "", city: initialValues.destination, country: "" } : null,
    );
    const [hotelCheckIn, setHotelCheckIn] = useState(initialValues.checkIn ?? inDays(14));
    const [hotelCheckOut, setHotelCheckOut] = useState(initialValues.checkOut ?? inDays(17));
    const [hotelGuests, setHotelGuests] = useState({ adults: Number(initialValues.adults ?? "2"), children: Number(initialValues.children ?? "0") });
    const [hotelRooms, setHotelRooms] = useState(Number(initialValues.rooms ?? "1"));

    /* ---- Cars state ---- */
    const [carPickup, setCarPickup] = useState<AirportSuggestion | null>(
        initialValues.pickupLocation ? { code: initialValues.pickupLocation, name: "", city: initialValues.pickupLocation, country: "" } : null,
    );
    const [carDropoff, setCarDropoff] = useState<AirportSuggestion | null>(
        initialValues.dropoffLocation ? { code: initialValues.dropoffLocation, name: "", city: initialValues.dropoffLocation, country: "" } : null,
    );
    const [carPickupDate, setCarPickupDate] = useState(initialValues.pickupDate ?? inDays(14));
    const [carDropoffDate, setCarDropoffDate] = useState(initialValues.dropoffDate ?? inDays(19));
    const [carPickupTime, setCarPickupTime] = useState(initialValues.pickupTime ?? "10:00");
    const [carDropoffTime, setCarDropoffTime] = useState(initialValues.dropoffTime ?? "10:00");

    /* ---- Activities state ---- */
    const [actDest, setActDest] = useState<AirportSuggestion | null>(
        initialValues.destination && initialTab === "activities" ? { code: initialValues.destination, name: "", city: initialValues.destination, country: "" } : null,
    );
    const [actFrom, setActFrom] = useState(initialValues.from ?? inDays(14));
    const [actTo, setActTo] = useState(initialValues.to ?? "");
    const [actTravelers, setActTravelers] = useState({ adults: Number(initialValues.adults ?? "2"), children: Number(initialValues.children ?? "0") });

    /* ---- Submit ---- */
    const handleSubmit = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();
            const params = new URLSearchParams();
            let url = "";
            let label = "";

            switch (tab) {
                case "flights": {
                    const o = flightOrigin?.code ?? "JFK";
                    const d = flightDest?.code ?? "LHR";
                    params.set("origin", o);
                    params.set("destination", d);
                    params.set("depart", flightDepart);
                    if (tripType === "roundtrip" && flightReturn) params.set("return", flightReturn);
                    params.set("cabin", cabin);
                    params.set("adults", String(passengers.adults));
                    if (passengers.children > 0) params.set("children", String(passengers.children));
                    if (passengers.infants > 0) params.set("infants", String(passengers.infants));
                    url = `/flights?${params.toString()}`;
                    label = `${o} → ${d} · ${flightDepart}`;
                    break;
                }
                case "hotels": {
                    const dest = hotelDest?.city || hotelDest?.code || "Paris";
                    params.set("destination", dest);
                    params.set("checkIn", hotelCheckIn);
                    params.set("checkOut", hotelCheckOut);
                    params.set("adults", String(hotelGuests.adults));
                    if (hotelGuests.children > 0) params.set("children", String(hotelGuests.children));
                    params.set("rooms", String(hotelRooms));
                    url = `/hotels?${params.toString()}`;
                    label = `Hotels in ${dest} · ${hotelCheckIn}`;
                    break;
                }
                case "cars": {
                    const p = carPickup?.code || carPickup?.city || "JFK";
                    params.set("pickupLocation", p);
                    if (carDropoff) params.set("dropoffLocation", carDropoff.code || carDropoff.city);
                    params.set("pickupDate", carPickupDate);
                    params.set("dropoffDate", carDropoffDate);
                    params.set("pickupTime", carPickupTime);
                    params.set("dropoffTime", carDropoffTime);
                    url = `/cars?${params.toString()}`;
                    label = `Car from ${p} · ${carPickupDate}`;
                    break;
                }
                case "activities": {
                    const dest = actDest?.city || actDest?.code || "Tokyo";
                    params.set("destination", dest);
                    params.set("from", actFrom);
                    if (actTo) params.set("to", actTo);
                    params.set("adults", String(actTravelers.adults));
                    if (actTravelers.children > 0) params.set("children", String(actTravelers.children));
                    url = `/activities?${params.toString()}`;
                    label = `Activities in ${dest} · ${actFrom}`;
                    break;
                }
            }

            saveRecent({ tab, label, url, ts: Date.now() });
            setRecentSearches(loadRecent());
            router.push(url);
        },
        [
            tab, router,
            flightOrigin, flightDest, flightDepart, flightReturn, tripType, passengers, cabin,
            hotelDest, hotelCheckIn, hotelCheckOut, hotelGuests, hotelRooms,
            carPickup, carDropoff, carPickupDate, carDropoffDate, carPickupTime, carDropoffTime,
            actDest, actFrom, actTo, actTravelers,
        ],
    );

    const searchLabel = `Search ${tab.charAt(0).toUpperCase() + tab.slice(1)}`;

    return (
        <div className={cn("w-full", compact ? "mt-4" : "mt-0", className)}>
            {/* Tab bar */}
            <div
                role="tablist"
                aria-label="Search type"
                className={cn(
                    "inline-flex flex-wrap items-center gap-1 p-1",
                    compact
                        ? "rounded-full bg-white border border-ink-900/10 shadow-subtle"
                        : "rounded-full bg-white/80 backdrop-blur-sm border border-white/30 shadow-subtle",
                )}
            >
                {TABS.map((t) => {
                    const active = tab === t.id;
                    return (
                        <button
                            key={t.id}
                            role="tab"
                            aria-selected={active}
                            onClick={() => setTab(t.id)}
                            type="button"
                            className={cn(
                                "inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-200",
                                active
                                    ? "bg-ink-900 text-cream-100 shadow-card"
                                    : "text-ink-500 hover:text-ink-900 hover:bg-ink-900/[0.04]",
                                active && !compact && "border-b-2 border-amber-500",
                            )}
                        >
                            <t.Icon className="h-4 w-4" strokeWidth={2.25} />
                            <span className="hidden sm:inline">{t.label}</span>
                        </button>
                    );
                })}
            </div>

            {/* Form card (glass variant for hero, solid for compact) */}
            <form
                onSubmit={handleSubmit}
                className={cn(
                    "mt-3 rounded-2xl border",
                    compact
                        ? "shadow-card bg-ink-50/80 border-ink-900/10 p-3 sm:p-4"
                        : "bg-white/60 backdrop-blur-md border-white/30 shadow-glass p-4 sm:p-5",
                )}
            >
                {/* Flights */}
                {tab === "flights" && (
                    <div className="space-y-3">
                        {/* Trip type toggle */}
                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                onClick={() => setTripType("roundtrip")}
                                className={cn(
                                    "px-3 py-1.5 rounded-full text-xs font-semibold transition-all",
                                    tripType === "roundtrip"
                                        ? "bg-amber-500 text-white shadow-subtle"
                                        : "bg-white/60 text-ink-500 border border-ink-200 hover:bg-cream-100",
                                )}
                            >
                                Round-trip
                            </button>
                            <button
                                type="button"
                                onClick={() => {
                                    setTripType("oneway");
                                    setFlightReturn("");
                                }}
                                className={cn(
                                    "px-3 py-1.5 rounded-full text-xs font-semibold transition-all",
                                    tripType === "oneway"
                                        ? "bg-amber-500 text-white shadow-subtle"
                                        : "bg-white/60 text-ink-500 border border-ink-200 hover:bg-cream-100",
                                )}
                            >
                                One-way
                            </button>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_auto_1fr_1fr_1fr] gap-4 lg:gap-3 items-start">
                            <AutocompleteInput
                                label="From"
                                value={flightOrigin}
                                onChange={setFlightOrigin}
                                placeholder="City or airport"
                                name="origin"
                            />
                            <div className="hidden lg:flex items-center justify-center pt-5" aria-hidden>
                                <button
                                    type="button"
                                    onClick={() => {
                                        const tmp = flightOrigin;
                                        setFlightOrigin(flightDest);
                                        setFlightDest(tmp);
                                    }}
                                    className="flex h-8 w-8 items-center justify-center rounded-full border border-ink-200 text-ink-400 hover:bg-ink-50 hover:text-ink-600 transition-colors"
                                >
                                    <ArrowRightLeft className="h-3.5 w-3.5" />
                                </button>
                            </div>
                            <AutocompleteInput
                                label="To"
                                value={flightDest}
                                onChange={setFlightDest}
                                placeholder="City or airport"
                                name="destination"
                            />
                            <DatePicker
                                label="Depart"
                                value={flightDepart}
                                onChange={setFlightDepart}
                                name="depart"
                                required
                            />
                            {tripType === "roundtrip" && (
                                <DatePicker
                                    label="Return"
                                    value={flightReturn}
                                    onChange={setFlightReturn}
                                    name="return"
                                    minDate={flightDepart}
                                />
                            )}
                        </div>

                        <div className="flex flex-wrap items-end gap-4 lg:gap-6">
                            <PassengerSelector
                                adults={passengers.adults}
                                children={passengers.children}
                                infants={passengers.infants}
                                onChange={setPassengers}
                            />
                            <CabinSelector value={cabin} onChange={setCabin} />
                            <div className="ml-auto">
                                <SearchButton label={searchLabel} />
                            </div>
                        </div>
                    </div>
                )}

                {/* Hotels */}
                {tab === "hotels" && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr] gap-4 lg:gap-3 items-start">
                            <AutocompleteInput
                                label="Destination"
                                value={hotelDest}
                                onChange={setHotelDest}
                                placeholder="City or hotel"
                                cityMode
                                name="destination"
                            />
                            <DatePicker
                                label="Check-in"
                                value={hotelCheckIn}
                                onChange={setHotelCheckIn}
                                name="checkIn"
                                required
                            />
                            <DatePicker
                                label="Check-out"
                                value={hotelCheckOut}
                                onChange={setHotelCheckOut}
                                name="checkOut"
                                minDate={hotelCheckIn}
                                required
                            />
                        </div>
                        <div className="flex flex-wrap items-end gap-4 lg:gap-6">
                            <GuestSelector
                                adults={hotelGuests.adults}
                                children={hotelGuests.children}
                                onChange={setHotelGuests}
                                showRooms
                                rooms={hotelRooms}
                                onRoomsChange={setHotelRooms}
                            />
                            <div className="ml-auto">
                                <SearchButton label={searchLabel} />
                            </div>
                        </div>
                    </div>
                )}

                {/* Cars */}
                {tab === "cars" && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_1fr] gap-4 lg:gap-3 items-start">
                            <AutocompleteInput
                                label="Pickup location"
                                value={carPickup}
                                onChange={setCarPickup}
                                placeholder="Airport or city"
                                name="pickupLocation"
                            />
                            <AutocompleteInput
                                label="Drop-off location"
                                value={carDropoff}
                                onChange={setCarDropoff}
                                placeholder="Same as pickup"
                                name="dropoffLocation"
                            />
                        </div>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 lg:gap-3 items-start">
                            <DatePicker
                                label="Pickup date"
                                value={carPickupDate}
                                onChange={setCarPickupDate}
                                name="pickupDate"
                                required
                            />
                            <div className="flex flex-col gap-1.5">
                                <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                                    Pickup time
                                </span>
                                <input
                                    type="time"
                                    name="pickupTime"
                                    value={carPickupTime}
                                    onChange={(e) => setCarPickupTime(e.target.value)}
                                    className="w-full border-0 bg-transparent p-0 text-[0.95rem] font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                                />
                            </div>
                            <DatePicker
                                label="Drop-off date"
                                value={carDropoffDate}
                                onChange={setCarDropoffDate}
                                name="dropoffDate"
                                minDate={carPickupDate}
                                required
                            />
                            <div className="flex flex-col gap-1.5">
                                <span className="text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                                    Drop-off time
                                </span>
                                <input
                                    type="time"
                                    name="dropoffTime"
                                    value={carDropoffTime}
                                    onChange={(e) => setCarDropoffTime(e.target.value)}
                                    className="w-full border-0 bg-transparent p-0 text-[0.95rem] font-semibold text-ink-900 focus:ring-0 focus:outline-none"
                                />
                            </div>
                        </div>
                        <div className="flex justify-end">
                            <SearchButton label={searchLabel} />
                        </div>
                    </div>
                )}

                {/* Activities */}
                {tab === "activities" && (
                    <div className="space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr] gap-4 lg:gap-3 items-start">
                            <AutocompleteInput
                                label="Destination"
                                value={actDest}
                                onChange={setActDest}
                                placeholder="City or attraction"
                                cityMode
                                name="destination"
                            />
                            <DatePicker
                                label="From"
                                value={actFrom}
                                onChange={setActFrom}
                                name="from"
                                required
                            />
                            <DatePicker
                                label="To (optional)"
                                value={actTo}
                                onChange={setActTo}
                                name="to"
                                minDate={actFrom}
                            />
                        </div>
                        <div className="flex flex-wrap items-end gap-4 lg:gap-6">
                            <GuestSelector
                                adults={actTravelers.adults}
                                children={actTravelers.children}
                                onChange={setActTravelers}
                            />
                            <div className="ml-auto">
                                <SearchButton label={searchLabel} />
                            </div>
                        </div>
                    </div>
                )}
            </form>

            {/* Recent searches */}
            {recentSearches.length > 0 && !compact && (
                <div className="mt-4 flex flex-wrap items-center gap-2">
                    <span className="flex items-center gap-1.5 text-xs font-medium text-ink-400">
                        <Clock className="h-3 w-3" />
                        Recent:
                    </span>
                    {recentSearches.map((r, i) => (
                        <button
                            key={r.ts}
                            type="button"
                            onClick={() => router.push(r.url)}
                            className={cn(
                                "inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium",
                                "bg-white/80 backdrop-blur-sm border border-ink-200/40 text-ink-600",
                                "hover:bg-white hover:border-ink-300 hover:text-ink-900 transition-all",
                                "shadow-subtle",
                            )}
                        >
                            {TABS.find((t) => t.id === r.tab)?.Icon && (
                                <span className="text-ink-400">
                                    {(() => {
                                        const TabIcon = TABS.find((t) => t.id === r.tab)!.Icon;
                                        return <TabIcon className="h-3 w-3" />;
                                    })()}
                                </span>
                            )}
                            <span className="truncate max-w-[180px]">{r.label}</span>
                            <span
                                role="button"
                                tabIndex={0}
                                onClick={(e) => {
                                    e.stopPropagation();
                                    const updated = loadRecent().filter((_, idx) => idx !== i);
                                    localStorage.setItem(RECENT_KEY, JSON.stringify(updated));
                                    setRecentSearches(updated);
                                }}
                                onKeyDown={(e) => {
                                    if (e.key === "Enter" || e.key === " ") {
                                        e.stopPropagation();
                                        const updated = loadRecent().filter((_, idx) => idx !== i);
                                        localStorage.setItem(RECENT_KEY, JSON.stringify(updated));
                                        setRecentSearches(updated);
                                    }
                                }}
                                className="text-ink-300 hover:text-ink-600"
                                aria-label="Remove recent search"
                            >
                                <X className="h-3 w-3" />
                            </span>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
