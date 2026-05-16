import { NextRequest, NextResponse } from "next/server";

/**
 * Mock airport/city autocomplete endpoint.
 * GET /api/autocomplete?q=...&type=airport|city
 *
 * In production this would query a real aviation data API.
 * For now, returns from a curated static dataset.
 */

interface AirportResult {
    code: string;
    name: string;
    city: string;
    country: string;
    type: "airport" | "city";
}

const AIRPORTS: AirportResult[] = [
    { code: "JFK", name: "John F. Kennedy International", city: "New York", country: "United States", type: "airport" },
    { code: "LAX", name: "Los Angeles International", city: "Los Angeles", country: "United States", type: "airport" },
    { code: "ORD", name: "O'Hare International", city: "Chicago", country: "United States", type: "airport" },
    { code: "ATL", name: "Hartsfield-Jackson Atlanta International", city: "Atlanta", country: "United States", type: "airport" },
    { code: "DFW", name: "Dallas/Fort Worth International", city: "Dallas", country: "United States", type: "airport" },
    { code: "DEN", name: "Denver International", city: "Denver", country: "United States", type: "airport" },
    { code: "SFO", name: "San Francisco International", city: "San Francisco", country: "United States", type: "airport" },
    { code: "SEA", name: "Seattle-Tacoma International", city: "Seattle", country: "United States", type: "airport" },
    { code: "MIA", name: "Miami International", city: "Miami", country: "United States", type: "airport" },
    { code: "BOS", name: "Logan International", city: "Boston", country: "United States", type: "airport" },
    { code: "EWR", name: "Newark Liberty International", city: "Newark", country: "United States", type: "airport" },
    { code: "LGA", name: "LaGuardia", city: "New York", country: "United States", type: "airport" },
    { code: "IAD", name: "Washington Dulles International", city: "Washington D.C.", country: "United States", type: "airport" },
    { code: "LHR", name: "Heathrow", city: "London", country: "United Kingdom", type: "airport" },
    { code: "LGW", name: "Gatwick", city: "London", country: "United Kingdom", type: "airport" },
    { code: "STN", name: "Stansted", city: "London", country: "United Kingdom", type: "airport" },
    { code: "CDG", name: "Charles de Gaulle", city: "Paris", country: "France", type: "airport" },
    { code: "ORY", name: "Orly", city: "Paris", country: "France", type: "airport" },
    { code: "FRA", name: "Frankfurt Airport", city: "Frankfurt", country: "Germany", type: "airport" },
    { code: "MUC", name: "Munich Airport", city: "Munich", country: "Germany", type: "airport" },
    { code: "AMS", name: "Schiphol", city: "Amsterdam", country: "Netherlands", type: "airport" },
    { code: "MAD", name: "Adolfo Suárez Madrid-Barajas", city: "Madrid", country: "Spain", type: "airport" },
    { code: "BCN", name: "El Prat", city: "Barcelona", country: "Spain", type: "airport" },
    { code: "FCO", name: "Fiumicino", city: "Rome", country: "Italy", type: "airport" },
    { code: "MXP", name: "Malpensa", city: "Milan", country: "Italy", type: "airport" },
    { code: "IST", name: "Istanbul Airport", city: "Istanbul", country: "Turkey", type: "airport" },
    { code: "DXB", name: "Dubai International", city: "Dubai", country: "United Arab Emirates", type: "airport" },
    { code: "SIN", name: "Changi", city: "Singapore", country: "Singapore", type: "airport" },
    { code: "HND", name: "Haneda", city: "Tokyo", country: "Japan", type: "airport" },
    { code: "NRT", name: "Narita International", city: "Tokyo", country: "Japan", type: "airport" },
    { code: "ICN", name: "Incheon International", city: "Seoul", country: "South Korea", type: "airport" },
    { code: "HKG", name: "Hong Kong International", city: "Hong Kong", country: "China", type: "airport" },
    { code: "BKK", name: "Suvarnabhumi", city: "Bangkok", country: "Thailand", type: "airport" },
    { code: "SYD", name: "Sydney Kingsford Smith", city: "Sydney", country: "Australia", type: "airport" },
    { code: "MEL", name: "Melbourne Tullamarine", city: "Melbourne", country: "Australia", type: "airport" },
    { code: "YYZ", name: "Toronto Pearson International", city: "Toronto", country: "Canada", type: "airport" },
    { code: "YVR", name: "Vancouver International", city: "Vancouver", country: "Canada", type: "airport" },
    { code: "GRU", name: "São Paulo–Guarulhos International", city: "São Paulo", country: "Brazil", type: "airport" },
    { code: "MEX", name: "Benito Juárez International", city: "Mexico City", country: "Mexico", type: "airport" },
    { code: "CUN", name: "Cancún International", city: "Cancún", country: "Mexico", type: "airport" },
    { code: "DOH", name: "Hamad International", city: "Doha", country: "Qatar", type: "airport" },
    { code: "ZRH", name: "Zurich Airport", city: "Zurich", country: "Switzerland", type: "airport" },
    { code: "VIE", name: "Vienna International", city: "Vienna", country: "Austria", type: "airport" },
    { code: "CPH", name: "Copenhagen Airport", city: "Copenhagen", country: "Denmark", type: "airport" },
    { code: "OSL", name: "Oslo Gardermoen", city: "Oslo", country: "Norway", type: "airport" },
    { code: "LIS", name: "Humberto Delgado", city: "Lisbon", country: "Portugal", type: "airport" },
    { code: "ATH", name: "Athens International", city: "Athens", country: "Greece", type: "airport" },
    { code: "CAI", name: "Cairo International", city: "Cairo", country: "Egypt", type: "airport" },
    { code: "JNB", name: "O.R. Tambo International", city: "Johannesburg", country: "South Africa", type: "airport" },
    { code: "DEL", name: "Indira Gandhi International", city: "Delhi", country: "India", type: "airport" },
    { code: "BOM", name: "Chhatrapati Shivaji Maharaj International", city: "Mumbai", country: "India", type: "airport" },
];

export async function GET(request: NextRequest) {
    const { searchParams } = request.nextUrl;
    const q = (searchParams.get("q") ?? "").trim().toLowerCase();
    const typeFilter = searchParams.get("type") as "airport" | "city" | null;

    if (q.length < 1) {
        return NextResponse.json([]);
    }

    let results = AIRPORTS.filter((a) => {
        const searchable = `${a.code} ${a.name} ${a.city} ${a.country}`.toLowerCase();
        return searchable.includes(q);
    });

    if (typeFilter) {
        results = results.filter((a) => a.type === typeFilter);
    }

    // Return top 8 results
    return NextResponse.json(results.slice(0, 8));
}
