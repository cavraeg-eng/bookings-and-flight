"use client";

import { useState, useEffect, useCallback } from "react";
import { useRouter } from "next/navigation";
import { ArrowRightLeft } from "lucide-react";
import { cn } from "@/lib/cn";
import { inDays } from "@/lib/search";
import { AutocompleteInput, type AirportSuggestion } from "./AutocompleteInput";
import { DatePicker } from "./DatePicker";
import { PassengerSelector, type PassengerCounts } from "./PassengerSelector";
import { CabinSelector, type CabinClass } from "./CabinSelector";
import { GuestSelector, SearchButton } from "./SearchFormControls";
import { RecentSearches } from "./RecentSearches";
import { SearchTabs } from "./SearchTabs";
import {
    loadRecent,
    saveRecent,
    searchLocationValue,
    selectedFlightCode,
    suggestionDisplay,
    type RecentSearch,
    type SearchFormProps,
    type Vertical,
} from "./SearchFormSupport";

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
    const [formError, setFormError] = useState<string | null>(null);

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
    const [flightOriginQuery, setFlightOriginQuery] = useState(initialValues.origin ?? "");
    const [flightDestQuery, setFlightDestQuery] = useState(initialValues.destination ?? "");
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
    const [hotelDestQuery, setHotelDestQuery] = useState(initialValues.destination ?? "");
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
    const [carPickupQuery, setCarPickupQuery] = useState(initialValues.pickupLocation ?? "");
    const [carDropoffQuery, setCarDropoffQuery] = useState(initialValues.dropoffLocation ?? "");
    const [carPickupDate, setCarPickupDate] = useState(initialValues.pickupDate ?? inDays(14));
    const [carDropoffDate, setCarDropoffDate] = useState(initialValues.dropoffDate ?? inDays(19));
    const [carPickupTime, setCarPickupTime] = useState(initialValues.pickupTime ?? "10:00");
    const [carDropoffTime, setCarDropoffTime] = useState(initialValues.dropoffTime ?? "10:00");

    /* ---- Activities state ---- */
    const [actDest, setActDest] = useState<AirportSuggestion | null>(
        initialValues.destination && initialTab === "activities" ? { code: initialValues.destination, name: "", city: initialValues.destination, country: "" } : null,
    );
    const [actDestQuery, setActDestQuery] = useState(initialValues.destination && initialTab === "activities" ? initialValues.destination : "");
    const [actFrom, setActFrom] = useState(initialValues.from ?? inDays(14));
    const [actTo, setActTo] = useState(initialValues.to ?? "");
    const [actTravelers, setActTravelers] = useState({ adults: Number(initialValues.adults ?? "2"), children: Number(initialValues.children ?? "0") });

    /* ---- Submit ---- */
    const handleSubmit = useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();
            setFormError(null);
            const params = new URLSearchParams();
            let url = "";
            let label = "";

            switch (tab) {
                case "flights": {
                    const o = selectedFlightCode(flightOrigin, flightOriginQuery);
                    const d = selectedFlightCode(flightDest, flightDestQuery);
                    if (!o || !d) {
                        setFormError("Choose both airports from the suggestions before searching.");
                        return;
                    }
                    if (tripType === "roundtrip" && !flightReturn) {
                        setFormError("Choose a return date for a round-trip search.");
                        return;
                    }
                    params.set("origin", o);
                    params.set("destination", d);
                    params.set("depart", flightDepart);
                    if (tripType === "roundtrip") params.set("return", flightReturn);
                    params.set("cabin", cabin);
                    params.set("adults", String(passengers.adults));
                    if (passengers.children > 0) params.set("children", String(passengers.children));
                    if (passengers.infants > 0) params.set("infants", String(passengers.infants));
                    url = `/flights?${params.toString()}`;
                    label = `${o} → ${d} · ${flightDepart}`;
                    break;
                }
                case "hotels": {
                    const dest = searchLocationValue(hotelDest, hotelDestQuery, true);
                    if (!dest) {
                        setFormError("Enter a hotel destination before searching.");
                        return;
                    }
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
                    const p = searchLocationValue(carPickup, carPickupQuery);
                    const dropoff = searchLocationValue(carDropoff, carDropoffQuery);
                    if (!p) {
                        setFormError("Enter a pickup location before searching.");
                        return;
                    }
                    params.set("pickupLocation", p);
                    if (dropoff) params.set("dropoffLocation", dropoff);
                    params.set("pickupDate", carPickupDate);
                    params.set("dropoffDate", carDropoffDate);
                    params.set("pickupTime", carPickupTime);
                    params.set("dropoffTime", carDropoffTime);
                    url = `/cars?${params.toString()}`;
                    label = `Car from ${p} · ${carPickupDate}`;
                    break;
                }
                case "activities": {
                    const dest = searchLocationValue(actDest, actDestQuery, true);
                    if (!dest) {
                        setFormError("Enter an activity destination before searching.");
                        return;
                    }
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
            flightOrigin, flightDest, flightOriginQuery, flightDestQuery, flightDepart, flightReturn, tripType, passengers, cabin,
            hotelDest, hotelDestQuery, hotelCheckIn, hotelCheckOut, hotelGuests, hotelRooms,
            carPickup, carPickupQuery, carDropoff, carDropoffQuery, carPickupDate, carDropoffDate, carPickupTime, carDropoffTime,
            actDest, actDestQuery, actFrom, actTo, actTravelers,
        ],
    );

    const searchLabel = `Search ${tab.charAt(0).toUpperCase() + tab.slice(1)}`;

    return (
        <div className={cn("w-full", compact ? "mt-4" : "mt-0", className)}>
            {/* Tab bar */}
            <SearchTabs activeTab={tab} compact={compact} onChange={setTab} />

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
                {formError && (
                    <div className="mb-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-ink-700">
                        {formError}
                    </div>
                )}

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
                                onChange={(airport) => {
                                    setFlightOrigin(airport);
                                    setFlightOriginQuery(suggestionDisplay(airport));
                                    setFormError(null);
                                }}
                                onInputValueChange={(value) => {
                                    setFlightOriginQuery(value);
                                    if (flightOrigin && value !== suggestionDisplay(flightOrigin)) {
                                        setFlightOrigin(null);
                                    }
                                }}
                                placeholder="City or airport"
                                name="origin"
                            />
                            <div className="hidden lg:flex items-center justify-center pt-5" aria-hidden>
                                <button
                                    type="button"
                                    onClick={() => {
                                        const tmp = flightOrigin;
                                        const tmpQuery = flightOriginQuery;
                                        setFlightOrigin(flightDest);
                                        setFlightOriginQuery(flightDestQuery);
                                        setFlightDest(tmp);
                                        setFlightDestQuery(tmpQuery);
                                    }}
                                    className="flex h-8 w-8 items-center justify-center rounded-full border border-ink-200 text-ink-400 hover:bg-ink-50 hover:text-ink-600 transition-colors"
                                >
                                    <ArrowRightLeft className="h-3.5 w-3.5" />
                                </button>
                            </div>
                            <AutocompleteInput
                                label="To"
                                value={flightDest}
                                onChange={(airport) => {
                                    setFlightDest(airport);
                                    setFlightDestQuery(suggestionDisplay(airport));
                                    setFormError(null);
                                }}
                                onInputValueChange={(value) => {
                                    setFlightDestQuery(value);
                                    if (flightDest && value !== suggestionDisplay(flightDest)) {
                                        setFlightDest(null);
                                    }
                                }}
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
                                    onChange={(value) => {
                                        setFlightReturn(value);
                                        setFormError(null);
                                    }}
                                    name="return"
                                    minDate={flightDepart}
                                    required
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
                                onChange={(destination) => {
                                    setHotelDest(destination);
                                    setHotelDestQuery(suggestionDisplay(destination, true));
                                    setFormError(null);
                                }}
                                onInputValueChange={(value) => {
                                    setHotelDestQuery(value);
                                    if (hotelDest && value !== suggestionDisplay(hotelDest, true)) {
                                        setHotelDest(null);
                                    }
                                }}
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
                                onChange={(location) => {
                                    setCarPickup(location);
                                    setCarPickupQuery(suggestionDisplay(location));
                                    setFormError(null);
                                }}
                                onInputValueChange={(value) => {
                                    setCarPickupQuery(value);
                                    if (carPickup && value !== suggestionDisplay(carPickup)) {
                                        setCarPickup(null);
                                    }
                                }}
                                placeholder="Airport or city"
                                name="pickupLocation"
                            />
                            <AutocompleteInput
                                label="Drop-off location"
                                value={carDropoff}
                                onChange={(location) => {
                                    setCarDropoff(location);
                                    setCarDropoffQuery(suggestionDisplay(location));
                                }}
                                onInputValueChange={(value) => {
                                    setCarDropoffQuery(value);
                                    if (carDropoff && value !== suggestionDisplay(carDropoff)) {
                                        setCarDropoff(null);
                                    }
                                }}
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
                                onChange={(destination) => {
                                    setActDest(destination);
                                    setActDestQuery(suggestionDisplay(destination, true));
                                    setFormError(null);
                                }}
                                onInputValueChange={(value) => {
                                    setActDestQuery(value);
                                    if (actDest && value !== suggestionDisplay(actDest, true)) {
                                        setActDest(null);
                                    }
                                }}
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

            <RecentSearches
                compact={compact}
                recentSearches={recentSearches}
                onOpen={(url) => router.push(url)}
                onChange={setRecentSearches}
            />
        </div>
    );
}
