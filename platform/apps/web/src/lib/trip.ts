const TRIP_ALLIANCE_ID = "1094387";
const TRIP_SID = "2209817";
const TRAVELPAYOUTS_TRS = "47745";
const TRAVELPAYOUTS_MARKER = "165739";
const TRAVELPAYOUTS_LOCALE = "en";
const TRAVELPAYOUTS_CURRENCY = "USD";
const TRIP_TARGET_HOST = "https://www.trip.com/";
const FLIGHTS_PROMO_ID = "4132";
const HOTELS_PROMO_ID = "4038";

const CABIN_MAP = {
    economy: "ys",
    premium: "s",
    business: "c",
    first: "f",
} as const;

export const TRIP_WIDGET_SCRIPTS = {
    flights:
        "https://tpwgt.com/content?trs=47745&shmarker=165739&locale=en&curr=USD&powered_by=false&border_radius=0&plain=true&color_button=%232681ff&color_button_text=%23ffffff&color_border=%232681ff&promo_id=4132&campaign_id=121",
    hotels:
        "https://tpwgt.com/content?trs=47745&shmarker=165739&lang=www&layout=S10391&powered_by=false&campaign_id=121&promo_id=4038",
} as const;

type FlightCabin = keyof typeof CABIN_MAP;

export function buildTripFlightSearchUrl({
    origin,
    destination,
    depart,
    returnDate,
    cabin,
    currency = TRAVELPAYOUTS_CURRENCY,
}: {
    origin: string;
    destination: string;
    depart: string;
    returnDate?: string;
    cabin: FlightCabin;
    currency?: string;
}): string {
    const tripUrl = new URL("https://www.trip.com/flights/ShowFareFirst/");

    tripUrl.searchParams.set("dcity", origin.trim().toUpperCase());
    tripUrl.searchParams.set("acity", destination.trim().toUpperCase());
    tripUrl.searchParams.set("ddate", depart);
    tripUrl.searchParams.set("class", CABIN_MAP[cabin] ?? CABIN_MAP.economy);
    tripUrl.searchParams.set("quantity", "1");
    tripUrl.searchParams.set("currency", currency.toUpperCase());
    tripUrl.searchParams.set("flighttype", returnDate ? "D" : "S");

    if (returnDate) {
        tripUrl.searchParams.set("rdate", returnDate);
    }

    return tripUrl.toString();
}

export function buildTripFlightAffiliateUrl({
    origin,
    destination,
    depart,
    returnDate,
    cabin,
    currency = TRAVELPAYOUTS_CURRENCY,
}: {
    origin: string;
    destination: string;
    depart: string;
    returnDate?: string;
    cabin: FlightCabin;
    currency?: string;
}): string {
    return buildTravelpayoutsRedirect({
        promoId: FLIGHTS_PROMO_ID,
        currency,
        targetUrl: buildTripFlightSearchUrl({
            origin,
            destination,
            depart,
            returnDate,
            cabin,
            currency,
        }),
    });
}

export function buildTripHotelSearchUrl({
    destination,
    checkIn,
    checkOut,
}: {
    destination: string;
    checkIn: string;
    checkOut: string;
}): string {
    const tripUrl = new URL("https://www.trip.com/hotels/w/home");

    tripUrl.searchParams.set("allianceId", TRIP_ALLIANCE_ID);
    tripUrl.searchParams.set("sid", TRIP_SID);
    tripUrl.searchParams.set("checkin", toTripHotelDate(checkIn));
    tripUrl.searchParams.set("checkout", toTripHotelDate(checkOut));

    if (destination.trim()) {
        tripUrl.searchParams.set("city", destination.trim());
    }

    return tripUrl.toString();
}

export function buildTripHotelAffiliateUrl({
    destination,
    checkIn,
    checkOut,
    currency = TRAVELPAYOUTS_CURRENCY,
}: {
    destination: string;
    checkIn: string;
    checkOut: string;
    currency?: string;
}): string {
    return buildTravelpayoutsRedirect({
        promoId: HOTELS_PROMO_ID,
        currency,
        targetUrl: buildTripHotelSearchUrl({
            destination,
            checkIn,
            checkOut,
        }),
    });
}

export function formatCabinLabel(cabin: FlightCabin): string {
    return cabin.charAt(0).toUpperCase() + cabin.slice(1);
}

function buildTravelpayoutsRedirect({
    promoId,
    currency,
    targetUrl,
}: {
    promoId: string;
    currency: string;
    targetUrl: string;
}): string {
    const redirectUrl = new URL("https://tpwgt.com/r");

    redirectUrl.searchParams.set("trs", TRAVELPAYOUTS_TRS);
    redirectUrl.searchParams.set("locale", TRAVELPAYOUTS_LOCALE);
    redirectUrl.searchParams.set("curr", currency.toUpperCase());
    redirectUrl.searchParams.set("marker", TRAVELPAYOUTS_MARKER);
    redirectUrl.searchParams.set("p", promoId);
    redirectUrl.searchParams.set("source_type", "customlink");
    redirectUrl.searchParams.set("type", "click");
    redirectUrl.searchParams.set("target_host", TRIP_TARGET_HOST);
    redirectUrl.searchParams.set("product_type", "tp_manual");
    redirectUrl.searchParams.set("promo_kind", "widget");
    redirectUrl.searchParams.set("u", targetUrl);

    return redirectUrl.toString();
}

function toTripHotelDate(date: string): string {
    return date.replaceAll("-", "/");
}