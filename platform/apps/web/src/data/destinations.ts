/* ------------------------------------------------------------------ */
/*  Static destination data for SEO pages                              */
/* ------------------------------------------------------------------ */

export interface Destination {
  slug: string;
  name: string;
  country: string;
  countryCode: string;
  airportCode: string;
  tagline: string;
  description: string;
  bestTimeToVisit: string;
  avgFlightPrice: number;
  currency: string;
  highlights: string[];
  nearbyDestinations: string[];
  /** CSS gradient used as hero background */
  gradient: string;
  region: "europe" | "asia" | "americas" | "middle-east" | "africa" | "oceania";
}

export const destinations: Destination[] = [
  {
    slug: "london",
    name: "London",
    country: "United Kingdom",
    countryCode: "GB",
    airportCode: "LHR",
    tagline: "History Meets Modern Culture",
    description:
      "From Big Ben to Borough Market, London weaves centuries of royal history with cutting-edge art, theatre, and cuisine. Wander cobbled lanes, ride the Tube, and lose yourself in world-class museums — most of them free.",
    bestTimeToVisit: "April to September",
    avgFlightPrice: 350,
    currency: "USD",
    highlights: ["Big Ben", "Tower Bridge", "British Museum", "Camden Market"],
    nearbyDestinations: ["paris", "amsterdam", "dublin", "edinburgh"],
    gradient: "from-sky-700 via-ink-800 to-ink-900",
    region: "europe",
  },
  {
    slug: "paris",
    name: "Paris",
    country: "France",
    countryCode: "FR",
    airportCode: "CDG",
    tagline: "The City of Light Awaits",
    description:
      "Paris enchants with its café terraces, grand boulevards, and world-famous art. Whether you're gazing at the Eiffel Tower at dusk or tasting a perfect croissant, every corner tells a story.",
    bestTimeToVisit: "April to June, September to October",
    avgFlightPrice: 380,
    currency: "USD",
    highlights: ["Eiffel Tower", "Louvre Museum", "Montmartre", "Seine River Cruise"],
    nearbyDestinations: ["london", "amsterdam", "barcelona", "rome"],
    gradient: "from-amber-600 via-rose-700 to-ink-900",
    region: "europe",
  },
  {
    slug: "tokyo",
    name: "Tokyo",
    country: "Japan",
    countryCode: "JP",
    airportCode: "NRT",
    tagline: "Where Tradition Meets Tomorrow",
    description:
      "Tokyo is a neon-lit metropolis where ancient temples stand beside futuristic skyscrapers. From sushi bars in Tsukiji to cherry blossoms in Shinjuku Gyoen, the city is endlessly surprising.",
    bestTimeToVisit: "March to May, September to November",
    avgFlightPrice: 650,
    currency: "USD",
    highlights: ["Shibuya Crossing", "Senso-ji Temple", "Tsukiji Outer Market", "Akihabara"],
    nearbyDestinations: ["singapore", "bangkok", "bali", "sydney"],
    gradient: "from-rose-600 via-pink-700 to-ink-900",
    region: "asia",
  },
  {
    slug: "rome",
    name: "Rome",
    country: "Italy",
    countryCode: "IT",
    airportCode: "FCO",
    tagline: "Eternal City, Timeless Charm",
    description:
      "Rome layers nearly three millennia of globally influential art, architecture, and culture. Toss a coin in the Trevi Fountain, explore the Colosseum, and savour the finest pasta on earth.",
    bestTimeToVisit: "April to June, September to October",
    avgFlightPrice: 420,
    currency: "USD",
    highlights: ["Colosseum", "Vatican Museums", "Trevi Fountain", "Trastevere"],
    nearbyDestinations: ["paris", "barcelona", "lisbon", "prague"],
    gradient: "from-amber-700 via-orange-800 to-ink-900",
    region: "europe",
  },
  {
    slug: "barcelona",
    name: "Barcelona",
    country: "Spain",
    countryCode: "ES",
    airportCode: "BCN",
    tagline: "Art, Beach, and Endless Tapas",
    description:
      "Barcelona is Gaudí's playground — a sun-soaked Mediterranean city where gothic alleys meet surreal architecture, lively markets, and golden beaches that stretch for miles.",
    bestTimeToVisit: "May to June, September to October",
    avgFlightPrice: 390,
    currency: "USD",
    highlights: ["La Sagrada Familia", "Park Güell", "La Rambla", "Gothic Quarter"],
    nearbyDestinations: ["paris", "lisbon", "rome", "marrakech"],
    gradient: "from-orange-500 via-red-700 to-ink-900",
    region: "europe",
  },
  {
    slug: "amsterdam",
    name: "Amsterdam",
    country: "Netherlands",
    countryCode: "NL",
    airportCode: "AMS",
    tagline: "Canals, Culture, and Charm",
    description:
      "Amsterdam charms with its gabled canal houses, world-class museums, and thriving café culture. Rent a bike, float through the canals, and discover art from Rembrandt to modern masters.",
    bestTimeToVisit: "April to May, September",
    avgFlightPrice: 370,
    currency: "USD",
    highlights: ["Anne Frank House", "Rijksmuseum", "Vondelpark", "Jordaan District"],
    nearbyDestinations: ["london", "paris", "prague", "lisbon"],
    gradient: "from-orange-600 via-amber-700 to-ink-900",
    region: "europe",
  },
  {
    slug: "dubai",
    name: "Dubai",
    country: "United Arab Emirates",
    countryCode: "AE",
    airportCode: "DXB",
    tagline: "Futuristic Skyline, Desert Soul",
    description:
      "Dubai rises from the desert with record-breaking skyscrapers, luxurious resorts, and world-class shopping. Beyond the glitz, discover spice souks, desert dune adventures, and stunning coastline.",
    bestTimeToVisit: "November to March",
    avgFlightPrice: 520,
    currency: "USD",
    highlights: ["Burj Khalifa", "Dubai Mall", "Palm Jumeirah", "Gold Souk"],
    nearbyDestinations: ["istanbul", "marrakech", "singapore", "bangkok"],
    gradient: "from-amber-500 via-yellow-700 to-ink-900",
    region: "middle-east",
  },
  {
    slug: "bangkok",
    name: "Bangkok",
    country: "Thailand",
    countryCode: "TH",
    airportCode: "BKK",
    tagline: "Street Food Capital of the World",
    description:
      "Bangkok pulses with energy — ornate temples, floating markets, legendary street food, and a nightlife scene that never sleeps. It's affordable, warm, and endlessly photogenic.",
    bestTimeToVisit: "November to February",
    avgFlightPrice: 580,
    currency: "USD",
    highlights: ["Grand Palace", "Chatuchak Market", "Wat Arun", "Khao San Road"],
    nearbyDestinations: ["bali", "singapore", "tokyo", "dubai"],
    gradient: "from-emerald-600 via-teal-700 to-ink-900",
    region: "asia",
  },
  {
    slug: "cancun",
    name: "Cancún",
    country: "Mexico",
    countryCode: "MX",
    airportCode: "CUN",
    tagline: "Caribbean Paradise, Mayan Heritage",
    description:
      "Cancún pairs powdery white-sand beaches and turquoise Caribbean waters with ancient Mayan ruins just a day-trip away. All-inclusive resorts, cenotes, and vibrant nightlife complete the picture.",
    bestTimeToVisit: "December to April",
    avgFlightPrice: 280,
    currency: "USD",
    highlights: ["Chichén Itzá", "Isla Mujeres", "Cenote Ik Kil", "Hotel Zone Beach"],
    nearbyDestinations: ["new-york", "rio-de-janeiro", "cape-town", "lisbon"],
    gradient: "from-cyan-500 via-blue-700 to-ink-900",
    region: "americas",
  },
  {
    slug: "new-york",
    name: "New York",
    country: "United States",
    countryCode: "US",
    airportCode: "JFK",
    tagline: "The City That Never Sleeps",
    description:
      "New York is a kaleidoscope of neighborhoods, each with its own personality — from Manhattan's iconic skyline to Brooklyn's artisan culture. Broadway, Central Park, and world-class dining await.",
    bestTimeToVisit: "April to June, September to November",
    avgFlightPrice: 220,
    currency: "USD",
    highlights: ["Central Park", "Times Square", "Statue of Liberty", "Brooklyn Bridge"],
    nearbyDestinations: ["cancun", "london", "paris", "reykjavik"],
    gradient: "from-slate-600 via-zinc-700 to-ink-900",
    region: "americas",
  },
  {
    slug: "bali",
    name: "Bali",
    country: "Indonesia",
    countryCode: "ID",
    airportCode: "DPS",
    tagline: "Island of the Gods",
    description:
      "Bali enchants with its terraced rice paddies, volcanic mountains, and spiritual Hindu temples. Surf legendary waves, indulge in spa retreats, and watch fiery sunsets over the Indian Ocean.",
    bestTimeToVisit: "April to October",
    avgFlightPrice: 700,
    currency: "USD",
    highlights: ["Ubud Rice Terraces", "Tanah Lot Temple", "Seminyak Beach", "Mount Batur"],
    nearbyDestinations: ["bangkok", "singapore", "tokyo", "sydney"],
    gradient: "from-emerald-500 via-green-700 to-ink-900",
    region: "asia",
  },
  {
    slug: "istanbul",
    name: "Istanbul",
    country: "Turkey",
    countryCode: "TR",
    airportCode: "IST",
    tagline: "Where East Meets West",
    description:
      "Straddling two continents, Istanbul dazzles with its Ottoman palaces, Byzantine mosaics, and the aroma of fresh simit and Turkish coffee. The Grand Bazaar alone is a world unto itself.",
    bestTimeToVisit: "April to May, September to November",
    avgFlightPrice: 440,
    currency: "USD",
    highlights: ["Hagia Sophia", "Grand Bazaar", "Blue Mosque", "Bosphorus Cruise"],
    nearbyDestinations: ["rome", "dubai", "prague", "marrakech"],
    gradient: "from-red-700 via-rose-800 to-ink-900",
    region: "europe",
  },
  {
    slug: "lisbon",
    name: "Lisbon",
    country: "Portugal",
    countryCode: "PT",
    airportCode: "LIS",
    tagline: "Sun-Kissed Tiles and Pastéis",
    description:
      "Lisbon captivates with its pastel-painted buildings, cobblestone hills, and legendary custard tarts. Ride the iconic Tram 28, explore Alfama, and sip vinho verde overlooking the Tagus.",
    bestTimeToVisit: "March to October",
    avgFlightPrice: 360,
    currency: "USD",
    highlights: ["Belém Tower", "Alfama District", "Pastéis de Belém", "Tram 28"],
    nearbyDestinations: ["barcelona", "rome", "paris", "marrakech"],
    gradient: "from-yellow-500 via-amber-700 to-ink-900",
    region: "europe",
  },
  {
    slug: "prague",
    name: "Prague",
    country: "Czech Republic",
    countryCode: "CZ",
    airportCode: "PRG",
    tagline: "Fairytale Spires and Bohemian Spirit",
    description:
      "Prague's medieval Old Town, baroque churches, and Art Nouveau gems make it one of Europe's most photogenic capitals. Beer is cheaper than water, and the culture runs deep.",
    bestTimeToVisit: "May to September",
    avgFlightPrice: 400,
    currency: "USD",
    highlights: ["Charles Bridge", "Prague Castle", "Old Town Square", "Astronomical Clock"],
    nearbyDestinations: ["amsterdam", "rome", "istanbul", "london"],
    gradient: "from-violet-700 via-purple-800 to-ink-900",
    region: "europe",
  },
  {
    slug: "sydney",
    name: "Sydney",
    country: "Australia",
    countryCode: "AU",
    airportCode: "SYD",
    tagline: "Harbour Views and Golden Sands",
    description:
      "Sydney wraps around its glittering harbour, where the Opera House and Harbour Bridge form one of the world's most recognizable skylines. Surf at Bondi, hike coastal trails, and dine harbour-side.",
    bestTimeToVisit: "September to November, March to May",
    avgFlightPrice: 850,
    currency: "USD",
    highlights: ["Sydney Opera House", "Bondi Beach", "Harbour Bridge", "The Rocks"],
    nearbyDestinations: ["bali", "tokyo", "singapore", "cape-town"],
    gradient: "from-sky-500 via-blue-700 to-ink-900",
    region: "oceania",
  },
  {
    slug: "singapore",
    name: "Singapore",
    country: "Singapore",
    countryCode: "SG",
    airportCode: "SIN",
    tagline: "Garden City with a Gourmet Soul",
    description:
      "Singapore is a spotless city-state where futuristic architecture meets tropical gardens and arguably the world's best hawker food. Marina Bay Sands, Sentosa, and Little India offer endless variety.",
    bestTimeToVisit: "February to April",
    avgFlightPrice: 620,
    currency: "USD",
    highlights: ["Marina Bay Sands", "Gardens by the Bay", "Hawker Centres", "Sentosa Island"],
    nearbyDestinations: ["bali", "bangkok", "tokyo", "dubai"],
    gradient: "from-teal-500 via-emerald-700 to-ink-900",
    region: "asia",
  },
  {
    slug: "reykjavik",
    name: "Reykjavik",
    country: "Iceland",
    countryCode: "IS",
    airportCode: "KEF",
    tagline: "Fire, Ice, and Northern Lights",
    description:
      "Reykjavik is the gateway to Iceland's other-worldly landscapes — geysers, glaciers, volcanic black-sand beaches, and the shimmering Northern Lights. The world's northernmost capital punches well above its size.",
    bestTimeToVisit: "June to August (midnight sun), September to March (Northern Lights)",
    avgFlightPrice: 340,
    currency: "USD",
    highlights: ["Blue Lagoon", "Golden Circle", "Northern Lights", "Hallgrímskirkja"],
    nearbyDestinations: ["london", "new-york", "amsterdam", "edinburgh"],
    gradient: "from-indigo-600 via-blue-800 to-ink-900",
    region: "europe",
  },
  {
    slug: "marrakech",
    name: "Marrakech",
    country: "Morocco",
    countryCode: "MA",
    airportCode: "RAK",
    tagline: "Spice-Scented Medina Magic",
    description:
      "Marrakech intoxicates the senses — labyrinthine souks, fragrant spice stalls, and riads hidden behind plain walls that open into mosaic courtyards. The Atlas Mountains rise just beyond the city.",
    bestTimeToVisit: "March to May, September to November",
    avgFlightPrice: 450,
    currency: "USD",
    highlights: ["Jemaa el-Fnaa", "Majorelle Garden", "Bahia Palace", "Atlas Mountains"],
    nearbyDestinations: ["lisbon", "barcelona", "istanbul", "dubai"],
    gradient: "from-orange-700 via-red-800 to-ink-900",
    region: "africa",
  },
  {
    slug: "rio-de-janeiro",
    name: "Rio de Janeiro",
    country: "Brazil",
    countryCode: "BR",
    airportCode: "GIG",
    tagline: "Samba, Sun, and Sugarloaf",
    description:
      "Rio de Janeiro stuns with its dramatic landscape — Christ the Redeemer overlooking Copacabana and Ipanema beaches, lush Tijuca Forest, and the electric energy of Carnival.",
    bestTimeToVisit: "December to March",
    avgFlightPrice: 550,
    currency: "USD",
    highlights: ["Christ the Redeemer", "Copacabana Beach", "Sugarloaf Mountain", "Lapa Steps"],
    nearbyDestinations: ["cancun", "new-york", "lisbon", "cape-town"],
    gradient: "from-green-500 via-yellow-600 to-ink-900",
    region: "americas",
  },
  {
    slug: "cape-town",
    name: "Cape Town",
    country: "South Africa",
    countryCode: "ZA",
    airportCode: "CPT",
    tagline: "Where Mountains Meet the Ocean",
    description:
      "Cape Town sits at the foot of Table Mountain, surrounded by pristine beaches, world-class vineyards, and the rugged Cape Peninsula. Wildlife, adventure, and vibrant culture collide.",
    bestTimeToVisit: "November to March",
    avgFlightPrice: 680,
    currency: "USD",
    highlights: ["Table Mountain", "Cape of Good Hope", "V&A Waterfront", "Stellenbosch Wineries"],
    nearbyDestinations: ["marrakech", "dubai", "rio-de-janeiro", "sydney"],
    gradient: "from-blue-600 via-teal-700 to-ink-900",
    region: "africa",
  },
];

/** Lookup by slug */
export function getDestination(slug: string): Destination | undefined {
  return destinations.find((d) => d.slug === slug);
}

/** Get destination objects by slug list */
export function getDestinationsBySlug(slugs: string[]): Destination[] {
  return slugs
    .map((s) => destinations.find((d) => d.slug === s))
    .filter(Boolean) as Destination[];
}
