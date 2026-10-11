/* The website's fixed texts, copied word for word from its views
   (resources/views/welcome.blade.php and resources/views/front/**). They are
   written into the website's code rather than its admin panel, so the app
   carries them the same way. When a view changes, change it here too. */
import { grouped } from '../format';

const img = {
  heroPrius: require('../../assets/site/hero-prius.webp'),
  heroHilux: require('../../assets/site/hero-hilux.webp'),
  heroSantafe: require('../../assets/site/hero-santafe.webp'),
  cutMarkxWhite: require('../../assets/site/cut-markx-white.webp'),
  cutGlsBlack: require('../../assets/site/cut-gls-black.webp'),
  cutFitWhite: require('../../assets/site/cut-fit-white.webp'),
  cutHilux: require('../../assets/site/cut-hilux.webp'),
  cutSantafe: require('../../assets/site/cut-santafe.webp'),
  cutPrius: require('../../assets/site/cut-prius.webp'),
  cutHiace: require('../../assets/site/cut-hiace.webp'),
  bodySports: require('../../assets/site/body-sports.webp'),
  bodyConvertible: require('../../assets/site/body-convertible.webp'),
};

export const LOGO = require('../../assets/site/logo.png');

/* ---------------------------------------------------------------- brands */

/* The logos of the website's "Brand" menu (and the two stocked brands it has
   no logo for). A brand is matched by its name in the stock. */
export const BRAND_LOGOS = {
  Toyota: require('../../assets/site/brand-toyota.webp'),
  Honda: require('../../assets/site/brand-honda.webp'),
  Nissan: require('../../assets/site/brand-nissan.webp'),
  Suzuki: require('../../assets/site/brand-suzuki.webp'),
  Mazda: require('../../assets/site/brand-mazda.webp'),
  Mitsubishi: require('../../assets/site/brand-mitsubishi.webp'),
  Subaru: require('../../assets/site/brand-subaru.webp'),
  Lexus: require('../../assets/site/brand-lexus.webp'),
  BMW: require('../../assets/site/brand-bmw.webp'),
  'Mercedes-Benz': require('../../assets/site/brand-mercedes-benz.webp'),
  Mercedes: require('../../assets/site/brand-mercedes-benz.webp'),
  Volkswagen: require('../../assets/site/brand-volkswagen.webp'),
  Audi: require('../../assets/site/brand-audi.webp'),
  Volvo: require('../../assets/site/brand-volvo.webp'),
  Ford: require('../../assets/site/brand-ford.webp'),
  Jeep: require('../../assets/site/brand-jeep.webp'),
  'Land Rover': require('../../assets/site/brand-land-rover.webp'),
  Hyundai: require('../../assets/site/brand-hyundai.webp'),
  'Alfa Romeo': require('../../assets/site/brand-alfa-romeo.webp'),
  Daihatsu: require('../../assets/site/brand-daihatsu.webp'),
  Hino: require('../../assets/site/brand-hino.webp'),
  Isuzu: require('../../assets/site/brand-isuzu.webp'),
  Porsche: require('../../assets/site/brand-porsche.webp'),
};

/* The website's "Brand" menu, in its order. */
export const MENU_BRANDS = [
  'Toyota', 'Honda', 'Nissan', 'Suzuki', 'Mazda', 'Mitsubishi', 'Subaru', 'Lexus', 'BMW',
  'Mercedes-Benz', 'Volkswagen', 'Audi', 'Volvo', 'Ford', 'Jeep', 'Land Rover', 'Hyundai',
  'Alfa Romeo', 'Daihatsu', 'Hino',
];

/* ------------------------------------------------------------------ home */

export const HOME = {
  hero: [
    {
      eyebrow: '2024 HYBRID',
      title: 'TOYOTA PRIUS PRIME',
      subtitle: 'Eco-Friendly Technology with Exceptional Efficiency',
      specs: ['CVT', 'Hybrid', '8,500 Km'],
      image: img.heroPrius,
      search: 'Prius',
    },
    {
      eyebrow: '2026 NEW ARRIVAL',
      title: 'TOYOTA HILUX TRAVO',
      subtitle: 'Overland Plus Edition - Premium Off-Road Performance',
      specs: ['Automatic', 'Diesel 4WD', '0 Km'],
      image: img.heroHilux,
      search: 'Travo',
    },
    {
      eyebrow: '2025 PREMIUM',
      title: 'HYUNDAI SANTA FE',
      subtitle: 'Luxury SUV with Advanced Safety Features',
      specs: ['Automatic', 'Gasoline', '0 Km'],
      image: img.heroSantafe,
      search: 'Santa Fe',
    },
  ],

  search: {
    eyebrow: 'Find Your Vehicle',
    title: 'Search SBK stock faster',
    text: 'Select brand, model, price, and year. We will take you to the shop page with matching vehicles.',
    button: 'Search Cars',
    quick: [
      { label: 'View all stock', sort: 'default' },
      { label: 'Best price first', sort: 'price-low' },
      { label: 'Newest year', sort: 'year-new' },
    ],
  },

  brandIntro: {
    title: 'Shop by World Known Brand',
    text: 'SBK offers a wide range of leading automotive brands, combining reliability, innovation, and style for every driver.',
  },

  stats: {
    items: [
      ['15', 'Years in Business'],
      ['1070+', 'Unit Sold'],
      ['3,000+', 'International Clientele'],
      ['30+', 'Countries Covered'],
    ],
    title: 'Global Reach With Proven Expertise',
    text: 'SBK Auto Trading has gained the trust of happy clients by building a reputation for dependability, honesty, and quality.',
    button: 'Read More',
  },

  bestSeller: {
    title: "This Week's Best Seller",
    lead: 'View the most popular vehicles buyers are choosing this week.',
    button: 'View All Recent Seller',
  },

  why: {
    title: 'Why Choose SBK Global Auto Trading',
    items: [
      { title: 'Trusted Expertise', icon: 'shield-checkmark-outline', text: 'SBK has 15 years of experience in global automotive trading, sourcing vehicles from verified suppliers to ensure quality, reliability, and professional service.' },
      { title: 'Extensive Vehicle Selection', icon: 'car-sport-outline', text: 'Access a wide range of Japanese, European, and American vehicles, and trucks, offering choices for every requirement, budget, and industry need.' },
      { title: 'Transparent & Reliable', icon: 'document-text-outline', text: 'All vehicles come with authentic auction sheets and detailed condition reports, giving clients full clarity and confidence in every purchase.' },
      { title: 'Flexible Purchase Options', icon: 'cart-outline', text: 'Buy directly from our ready stock or participate in Japanese auctions, providing freedom, variety, and opportunities to secure the right vehicle.' },
      { title: 'Global Reach', icon: 'globe-outline', text: 'Serving clients across 30+ countries with reliable shipping, documentation, and logistics support, ensuring smooth international transactions.' },
      { title: 'Customer-Focused Service', icon: 'headset-outline', text: 'SBK prioritizes clear communication and after-sales assistance, guiding clients through every step for a seamless, worry-free trading experience.' },
    ],
  },

  members: {
    title: 'We are the Member of',
    items: [
      { name: 'Japanese Car Trade', image: require('../../assets/site/member-jct.webp') },
      { name: 'Aisha Trading LLC', image: require('../../assets/site/member-at.webp') },
      { name: 'TCV Partner Network', image: require('../../assets/site/member-tvc.webp') },
    ],
  },

  payment: {
    title: 'Pick Your Payment Method',
    transparentTitle: 'Transparent Payments',
    transparentText: "No Surprises Nothing matters more to us than providing a secure and clear payment process. You'll be informed about all costs upfront, including vehicle price, shipping, and fees, so there are no surprises, just a smooth and trusted experience from start to finish.",
    lead: 'Smooth, Secure Payments for Your Vehicle Purchase',
    optionsTitle: 'Our Payment Options at',
    optionsBrand: 'SBK Global Auto Trading',
    logos: [require('../../assets/site/pay-mastercard.webp'), require('../../assets/site/pay-visa.webp')],
  },

  upcoming: {
    title: 'The Exclusive Upcoming Collection',
    lead: 'Get an early look at vehicles that will be added soon.',
    button: 'View All Upcoming Cars',
  },

  arrivals: {
    title: 'New In-Demand Arrivals',
    lead: 'View our latest Japanese vehicle arrivals with clear prices and key details.',
    button: 'View All New Cars',
  },

  steps: {
    eyebrow: 'How It Works',
    title: 'Buy Your Dream Car in 3 Easy Steps',
    items: [
      { icon: 'chatbox-ellipses-outline', title: 'Talk to the Agent', text: 'Confirm details, total cost, payment steps, and questions with our team.' },
      { icon: 'car-outline', title: 'Browse and Select a Vehicle', text: 'Choose from Japanese vehicles and review photos, mileage, model year, and price.' },
      { icon: 'boat-outline', title: 'Confirm Your Shipment', text: 'We arrange export and shipment and keep you updated until departure.' },
    ],
  },

  auction: {
    eyebrow: 'Auction Buying Support',
    title: 'Tell Us What You Need, We Bid for You',
    text: 'Share your preferred model, budget, year, mileage, and condition. Our agent searches Japanese auctions and bids on your behalf for the right car at the right price.',
    button: 'Request Auction Support',
    image: img.cutHilux,
  },

  /* The website's body-type cards link nowhere yet; here each opens the shop
     on the nearest of the shop's own body-type filters. */
  bodyTypes: {
    eyebrow: 'Pick Your Perfect Match',
    title: 'Search by Body Type',
    button: 'Check Our Shop',
    items: [
      { name: 'Sedan', image: img.cutMarkxWhite, filter: { types: ['Sedan'] } },
      { name: 'SUV', image: img.cutGlsBlack, filter: { types: ['SUV'] } },
      { name: 'Sports', image: img.bodySports, filter: { types: ['Coupe'] } },
      { name: 'Convertible', image: img.bodyConvertible, filter: { types: ['Convertible / Roadster'] } },
      { name: 'Compact', image: img.cutFitWhite, filter: { types: ['Hatchback'] } },
      { name: 'Pick Up', image: img.cutHilux, filter: { types: ['Truck / Double Cab'] } },
      { name: 'Crossover', image: img.cutSantafe, filter: { types: ['SUV'] } },
      { name: 'Electric', image: img.cutPrius, filter: { search: 'Hybrid' } },
    ],
  },

  videos: {
    title: 'Customer By Client Video',
    items: [
      { id: 'client-1', path: '/assets/front/video/client-1.mp4', poster: require('../../assets/site/video-1.webp'), ratio: 1 },
      { id: 'client-2', path: '/assets/front/video/client-2.mp4', poster: require('../../assets/site/video-2.webp'), ratio: 9 / 16 },
      { id: 'client-3', path: '/assets/front/video/client-3.mp4', poster: require('../../assets/site/video-3.webp'), ratio: 9 / 16 },
    ],
  },

  testimonials: {
    eyebrow: 'Testimonials',
    title: 'Trusted By 1000+ Car Lovers',
    text: 'Real buyers trust SBK for clear communication, inspected vehicles, and smooth export support.',
    items: [
      {
        quote: 'I was not sure how to buy a car from Japan, but the SBK team explained each step properly. They shared the vehicle details, price, and shipping updates on time.',
        initials: 'DM',
        name: 'Daniel M.',
        place: 'New York, NY',
      },
    ],
  },

  register: {
    eyebrow: 'Ready to Import?',
    title: 'Register with SBK Today for Unbeatable Prices',
    text: 'Tell us what you need, and our team will help you buy a car from Japan through a simple process.',
    button: 'Register Now',
    image: img.cutHiace,
  },

  guides: {
    eyebrow: 'Find out Now',
    title: 'Latest Guides for Car Buyers',
    button: 'View More',
  },

  faqImage: img.cutSantafe,
};

/* The home page's questions. Every one has the same answer on the website. */
export const FAQ = {
  title: 'Frequently Asked Questions',
  answer: 'Our team can guide you with details and next steps. Contact SBK for current requirements and availability.',
  questions: [
    'Where is your showroom located?',
    'How can I know if I can import a vehicle into my country?',
    'Where do you source your vehicles from?',
    'How do I purchase a vehicle through SBK?',
    'What deposit is required to start the search?',
    'Who handles export and shipping?',
    'How long does shipping take?',
    'What is the payment structure?',
  ],
};

/* The home search's own choices. */
export const PRICE_RANGES = [
  { label: 'Under $5,000', min: 0, max: 5000 },
  { label: '$5,000 - $10,000', min: 5000, max: 10000 },
  { label: '$10,000 - $20,000', min: 10000, max: 20000 },
  { label: '$20,000 - $40,000', min: 20000, max: 40000 },
  { label: '$40,000+', min: 40000, max: null },
];

export const MILEAGES = [50000, 100000, 150000, 200000, 250000].map((m) => ({
  label: 'Under ' + grouped(m) + ' km',
  value: m,
}));

export const TRANSMISSIONS = [
  { label: 'Automatic Transmission', value: 'Automatic' },
  { label: 'Manual Transmission', value: 'Manual' },
];

export const SORTS = [
  { label: 'Featured', value: 'default' },
  { label: 'Price: Low to High', value: 'price-low' },
  { label: 'Price: High to Low', value: 'price-high' },
  { label: 'Year: Newest', value: 'year-new' },
];

/* ------------------------------------------------------------- about us */

export const ABOUT = {
  eyebrow: 'ABOUT SBK',
  title: 'About Us',
  image: require('../../assets/site/about-building.webp'),
  experience: ['15+', 'Years of Experience'],
  whoLabel: 'WHO WE ARE',
  whoTitle: 'Global Auto Trading,',
  whoTitleRed: 'Built on Experience',
  who: [
    'SBK Auto Trading is an international vehicle importer and export company with 15 years of experience in the automotive trading industry.',
    'We focus on providing quality vehicles, transparent service and professional support to customers worldwide. Our experienced team helps customers through every stage of their vehicle purchase, from selection to export and shipment.',
    "Whether you're looking for a reliable family vehicle, commercial vehicle or your next dream car, our goal is to make the buying process simple, secure and dependable.",
  ],
  signature: ['SBK Global Auto Trading', 'Premium Vehicles Worldwide'],
  bookCar: 'Book a Car!',
  ceo: {
    image: require('../../assets/site/about-ceo.webp'),
    label: 'MESSAGE FROM THE CEO',
    title: 'Driven by Trust.',
    titleRed: 'Powered by Experience.',
    text: [
      'Finding reliable vehicles internationally often feels uncertain. Unclear pricing, hidden issues and weak support can make the process difficult.',
      'At SBK Global Auto Trading, our focus is on transparency, dependable customer service and making international vehicle purchasing as simple as possible.',
    ],
    name: ['SBK Auto Trading', 'Global Automotive Trading'],
  },
  values: [
    {
      icon: 'eye-outline',
      title: 'Our Vision',
      lead: 'Raising the Standard of Global Auto Trading',
      text: 'To build a globally recognized automotive trading brand by delivering consistent quality, transparent processes, and dependable service across international markets.',
    },
    {
      icon: 'flag-outline',
      title: 'Our Mission',
      lead: 'Reliable Sourcing. Worldwide Access.',
      text: 'To provide clients with access to quality vehicles through expert sourcing, clear processes, and exceptional global logistics, ensuring every transaction is professional and dependable.',
    },
  ],
  cta: {
    eyebrow: 'GLOBAL VEHICLE SOURCING',
    title: 'For Buyers and Trade Partners',
    text: 'Individual buyers and trade partners can work with SBK to find vehicles matching their requirements, whether sourcing one vehicle or managing bulk purchases.',
    button: 'Get Vehicles With Our Team',
    image: require('../../assets/site/about-agent.webp'),
  },
};

/* ------------------------------------------------------------- services */

export const SERVICES = {
  title: 'SBK Services',
  introTitle: 'Our Services - End-to-End Vehicle Export Management',
  intro: "Structured sourcing. Disciplined bidding. Managed logistics. Complete documentation support. Our services bring the entire Japanese vehicle export process under one coordinated system. Each step is handled with clarity, from vehicle selection to final shipment. We work with car dealers, resellers, fleet and rental operators, import-export businesses, bulk buyers, construction companies, taxi services, and individual buyers seeking reliable access to Japan's vehicle market. When the process demands accuracy and oversight, our services provide the control needed to move forward with confidence.",
  items: [
    {
      title: 'Japanese Vehicle Sourcing',
      text: 'We source vehicles directly from Japanese auctions and trusted dealer networks. You get access to verified listings, clear specifications, and real market value. We shortlist options based on your budget and destination requirements, so you review serious choices, not random inventory.',
      button: 'View Stock',
      image: require('../../assets/site/service-sourcing.webp'),
      dark: true,
    },
    {
      title: 'Auction Bidding and Consultation',
      text: 'Japanese auctions can be complex. We simplify them. Our team explains auction grades, reviews condition reports, and recommends practical bid ranges. You stay informed at every step and make decisions with clarity, not pressure.',
      button: 'Start Consultation',
      image: require('../../assets/site/service-dealership.webp'),
      dark: false,
    },
    {
      title: 'Vehicle Inspection and Condition Verification',
      text: "Before any commitment, we carefully review the vehicle's condition. That includes auction sheet translation, mileage verification, and accident history checks. You receive a clear understanding of what you're buying before it moves forward.",
      button: 'Verify Vehicle',
      image: require('../../assets/site/service-customs.webp'),
      dark: true,
    },
    {
      title: 'Shipping and Logistics Management',
      text: 'We manage shipping from Japan to your destination port with structured coordination. Freight booking, export clearance, and shipment tracking are handled professionally. You receive timely updates while your vehicle moves through each stage.',
      button: 'Track Shipment',
      image: require('../../assets/site/service-logistics.webp'),
      dark: false,
    },
    {
      title: 'Documentation and Import Assistance',
      text: "Before any commitment, we carefully review the vehicle's condition. That includes auction sheet translation, mileage verification, and accident history checks. You receive a clear understanding of what you're buying before it moves forward.",
      button: 'Get Support',
      image: require('../../assets/site/service-parts.webp'),
      dark: true,
    },
  ],
  cta: {
    title: "Let's Structure Your Next Shipment",
    text: "Japanese vehicle sourcing requires clarity, timing, and the right execution partner. If you're planning your next purchase or bulk order, let's structure it properly from the start.",
    buttons: ['Start Consultation', 'Contact Our Team'],
    image: require('../../assets/site/service-cta-car.webp'),
  },
};

/* ------------------------------------------------------------- partners */

export const PARTNERS = {
  eyebrow: 'We are the Member of',
  title: 'Official Partners',
  text: 'SBK Auto Trading works with trusted automotive networks and trading partners to make global vehicle buying more transparent, verified, and shipment-ready.',
  buttons: ['View Partners', 'Verify With SBK'],
  panelLabel: 'SBK Partner Confidence',
  panelTitle: 'Connected sourcing for serious buyers.',
  proof: [
    ['01', 'Trusted trade access', 'Memberships that support stock discovery and coordination.'],
    ['02', 'Document-ready support', 'Guidance for purchase, export, and buyer verification.'],
    ['03', 'Worldwide customer care', 'SBK offices and partner coverage for multiple markets.'],
  ],
  strip: [
    ['Sourcing', 'Better market access', 'Partners help SBK locate reliable vehicle opportunities across key markets.'],
    ['Verification', 'Cleaner buyer decisions', 'Membership-backed processes give customers stronger confidence before purchase.'],
    ['Export', 'Smoother handover', 'Our team coordinates next steps from documents to shipment readiness.'],
  ],
  networkTitle: 'Recognized partner network.',
  networkText: 'The official partner network supports SBK with dependable sourcing, clearer verification, and a more organized export buying experience.',
  network: [
    { name: 'TVC Partner Network', type: 'Official Partner', image: require('../../assets/site/member-tvc.webp'), summary: 'Partner-backed vehicle trading access for dependable stock discovery, purchase coordination, and export support.' },
    { name: 'Japanese Car Trade', type: 'Member Network', image: require('../../assets/site/member-jct.webp'), summary: 'A trusted Japanese vehicle trade network that supports transparent sourcing and documentation guidance.' },
    { name: 'Aisha Trading LLC', type: 'Trading Partner', image: require('../../assets/site/member-at.webp'), summary: 'Regional trading collaboration that strengthens SBK service coverage for individual and wholesale buyers.' },
  ],
  globalTitle: 'Partners Behind Our Global Network',
  globalText: "Our official partners extend SBK's sourcing and trading network across key automotive markets. By working with trusted companies, we can offer customers more vehicle options, stronger market access, and better support throughout the sourcing and export process.",
  global: [
    { name: 'JDM Trading Co. Ltd', type: 'Japan Market Partner', image: require('../../assets/site/partner-jdm.webp') },
    { name: 'Nazli Auto', type: 'Trading Network', image: require('../../assets/site/partner-nazli.webp') },
    { name: 'V Asia', type: 'Asia Export Partner', image: require('../../assets/site/partner-vasia.webp') },
  ],
  ctaTitle: 'Need partner verification or buying guidance?',
  ctaText: 'Talk to SBK before you commit. We can explain partner-backed sourcing, stock details, export documents, and the next step for your destination market.',
  ctaButton: 'Contact SBK',
};

/* ------------------------------------------------------------ locations */

export const LOCATIONS = {
  title: 'Our Offices Locations',
  introTitle: 'Worldwide',
  intro: 'SBK Global Auto Trading operates across key automotive markets to support vehicle sourcing, export, logistics and customer enquiries. Find your nearest SBK office and connect with a team that understands your market and vehicle requirements.',
  hours: 'Mon - Sat: 9:00 AM - 6:00 PM | Sunday: Closed',
  offices: [
    { title: 'Headquarter UAE', zone: 'Asia/Dubai', description: "Manages SBK's international operations, customer enquiries, vehicle sourcing, and global trading activities from Ajman.", address: 'Amber Gem Tower, Mezzanine Floor, Sheikh Khalifa Street, PO. Box 4848, Ajman, United Arab Emirates', phone: '+971 55 417 7311', map: 'Amber Gem Tower Ajman United Arab Emirates' },
    { title: 'USA Office', zone: 'America/Chicago', description: 'SBK Auto Trading USA office provides support and sourcing services for North American vehicles and clients.', address: 'Prior Lake, MN 55372, USA', phone: '+1 70-9427-5106', map: 'Prior Lake MN 55372 USA' },
    { title: 'Japan Office', zone: 'Asia/Tokyo', description: 'SBK Auto Trading Japan office handles direct Japanese auction sourcing, vehicle inspection, and worldwide export logistics.', address: 'Yokohama-Shi, Kanagawa-Ken, Japan', phone: '+81 70-9427-5106', map: 'Yokohama-Shi Kanagawa-Ken Japan' },
    { title: 'Kenya Office (Showroom Mombasa)', zone: 'Africa/Nairobi', description: 'SBK Auto Trading Kenya office is located in Mombasa, serving customers across Kenya and East Africa with ready stock vehicles.', address: 'Showroom No 5, Jdm Motors Behind Kra, Nyerere Road, Mombasa, Kenya', phone: '+254 705719918', map: 'Nyerere Road Mombasa Kenya' },
    { title: 'Jamaica Office', zone: 'America/Jamaica', description: 'SBK Auto Trading Jamaica office provides imported vehicle support, documentation management, and local client assistance.', address: '3 South Avenue, Kingston 5, Jamaica', phone: '+1 876 8036277 / +1 876 8772811', map: '3 South Avenue Kingston 5 Jamaica' },
    { title: 'Thailand Office', zone: 'Asia/Bangkok', description: 'SBK Auto Trading Thailand office manages vehicle logistics, export support, and regional commercial operations.', address: 'Bangkok, Thailand', phone: '+66 80-000-0000', map: 'Bangkok Thailand' },
    { title: 'Pakistan Office', zone: 'Asia/Karachi', description: 'SBK Auto Trading Pakistan office manages administrative operations, regional trade support, and customer consultations.', address: 'SB-36, Gulshan-e-Iqbal 13-A, Karachi, Pakistan', phone: '+92 213 339-3946 / +92 3708536560', map: 'SB-36 Gulshan-e-Iqbal 13-A Karachi Pakistan' },
  ],
};

/* ---------------------------------------------------------------- contact */

export const CONTACT_PAGE = {
  eyebrow: 'Contact SBK Auto Trading',
  title: 'Talk to a real export specialist before you buy.',
  text: 'Send your vehicle request, shipment question, or inspection concern to SBK. Our team can guide you through selection, verification, documentation, and export support.',
  fast: {
    title: 'Need a faster answer?',
    text: 'For urgent vehicle sourcing and shipment questions, message SBK on WhatsApp and include the car name, destination country, and budget range.',
    button: 'WhatsApp SBK',
  },
  pills: [
    ['01', 'Vehicle Sourcing', 'Tell us model, year, mileage, and target budget.'],
    ['02', 'Import Guidance', 'Ask about inspection, documents, and shipment steps.'],
    ['03', 'Global Offices', 'Reach SBK teams across UAE, Japan, USA, Kenya, Jamaica, and Pakistan.'],
  ],
  formLabel: 'Send Your Request',
  formTitle: 'We will get back to you with clear next steps.',
  messagePlaceholder: 'Tell us what you want to import, your destination country, and any specific questions.',
  success: 'Thank you. Your message has been submitted successfully.',
  include: [
    ['1', 'Vehicle Details', 'Make, model, year, mileage, and preferred condition.'],
    ['2', 'Destination', 'Country and port, so the team can guide shipment steps.'],
    ['3', 'Budget Range', 'Purchase budget plus import and delivery expectations.'],
  ],
  liveTitle: 'Live Vehicle Help',
  liveText: 'For quick availability checks, send screenshots or stock links directly to SBK on WhatsApp.',
  officesLabel: 'Global Reach',
  officesTitle: 'Find the nearest SBK office.',
  officesText: 'SBK supports international buyers through multiple offices and showroom locations, making vehicle export more transparent from inquiry to shipment.',
  offices: [
    { name: 'Headquarter UAE', phone: '+971 55 417 7311', address: 'Amber Gem Tower, Mezzanine Floor, Sheikh Khalifa Street, P.O. Box 4848, Ajman, United Arab Emirates', zone: 'Asia/Dubai' },
    { name: 'USA Office', phone: '+1 (321) 357-857', address: 'Prior Lake, MN 55372, USA', zone: 'America/Chicago' },
    { name: 'Japan Office', phone: '+81 70-9427-5106', address: 'Yokohama-Shi, Kanagawa-Ken, Japan', zone: 'Asia/Tokyo' },
    { name: 'Showroom Mombasa', phone: '+254 705719918', address: 'Nyere Avenue Showroom No 5 Jdm Motors Behind Kra Mombasa', zone: 'Africa/Nairobi' },
    { name: 'Jamaica Office', phone: '+1 876 8036277', address: '3 South Avenue Kingston 5, Jamaica', zone: 'America/Jamaica' },
    { name: 'Pakistan Office', phone: '+92 213 339-3946', address: 'SB-36, Gulshan-e-Iqbal 13-D Karachi, Pakistan', zone: 'Asia/Karachi' },
  ],
  closingTitle: 'Premium vehicles, shipped worldwide.',
  closingText: 'Whether you are buying from Japan, arranging export documents, or checking a car before it reaches your country, SBK is ready to help.',
};

/* ------------------------------------------------------------------- bank */

export const BANK = {
  eyebrow: 'Official Payment Accounts',
  title: 'Transfer with clarity, not guesswork.',
  text: 'Use the official SBK banking information below only after matching it with your invoice. Every field is arranged for quick checking before you send funds.',
  vaultTitle: 'Verify before transfer.',
  vaultText: 'Confirm your invoice, destination office, and account selection with the SBK team before making any bank transfer.',
  steps: [
    ['1', 'Match Invoice', 'Check the amount, currency, and beneficiary name.'],
    ['2', 'Use Listed Account', 'Select Japan, UAE, or USA exactly as instructed.'],
    ['3', 'Send Proof', 'Share the transfer receipt with SBK after payment.'],
  ],
  listLabel: 'Banking Details',
  listTitle: 'Official account directory.',
  listText: 'Choose the account shown on your invoice. Use the copy buttons carefully and always recheck beneficiary details before submitting a transfer.',
  banks: [
    {
      region: 'Japan',
      accent: 'JP',
      name: 'MITSUBISHI UFJ',
      subtitle: 'JDM Trading Co. Ltd',
      details: [
        ['Bank Name', 'MITSUBISHI UFJ'],
        ['Bank SWIFT Code', 'BOTKJPJT'],
        ['Bank Code', '0005'],
        ['Branch Name', 'TAKBATA BRANCH'],
        ['Branch Code', '297'],
        ['Branch Address', '1-203, TAKBATA, NAKAGAWA-KU, NAGOYA-SHI, AICHI 454-0911, JAPAN'],
        ['Account Number', '0282462'],
        ["Account Holder's Name (Beneficiary's Name)", 'JDM TRADING CO. LTD'],
        ["Account Holders' Address (Beneficiary's Address)", 'NAGOYASHI MIDORI-KU-SHIKAYAMA, 2-1-1- ROYAL SHIKAYAMA A503, JAPAN'],
        ["Account Holders' Contacts (Beneficiary's Contacts)", 'payment@sbkautotrading.com +81 70 9427 5106'],
      ],
    },
    {
      region: 'United Arab Emirates',
      accent: 'AE',
      name: 'Emirates Islamic',
      subtitle: 'SBK Global Auto Trading FZC LLC',
      details: [
        ['ACCOUNT', 'SBK GLOBAL AUTO TRADING FZC LLC'],
        ['BANK', 'Emirates Islamic'],
        ['BRANCH', 'EI Ibn Batuta Mall'],
        ['ADDRESS', 'Dubai, United Arab Emirates'],
        ['IBAN', 'AE020340003708512932302'],
        ['ACCOUNT NO', '3708512932302'],
        ['SWIFT CODE', 'MEBLAEADXXX'],
      ],
    },
    {
      region: 'United States',
      accent: 'US',
      name: 'US Bank',
      subtitle: 'SBK Global LLC',
      details: [
        ['ACCOUNT NAME', 'SBK Global LLC'],
        ['BANK', 'US Bank'],
        ['ROUTING', '091000022'],
        ['ACCOUNT NUMBER', '167505708112'],
        ['SWIFT', 'USBKUS44IMT'],
        ['ACCOUNT NO', '3708512932302'],
        ['BANK ADDRESS', '200 S 6th St Minneapolis, MN 55402'],
      ],
    },
  ],
  safetyTitle: 'Payment safety',
  safetyText: 'SBK recommends confirming bank details directly with your sales representative before transfer, especially for international payments.',
  confirmTitle: 'Need confirmation?',
  confirmText: 'Email payment@sbkautotrading.com or contact +81 70 9427 5106 before sending funds.',
};

/* ------------------------------------------------------- the other pages */

export const BLOG_PAGE = {
  eyebrow: 'SBK Blog',
  title: 'Simple guides for smarter vehicle imports.',
  text: 'Practical notes on choosing, checking, shipping, and protecting vehicles before you commit.',
  listLabel: 'Latest Articles',
  listTitle: 'Read the latest from SBK',
  helpTitle: 'Need help importing?',
  helpText: 'SBK can help you choose, verify, and ship the right vehicle with clear documents before you commit.',
  moreTitle: 'Explore more guides',
  moreText: 'Return to the blog for more buying, inspection, and export notes from SBK.',
};

export const PRODUCT_TEXT = {
  summary: 'Review this vehicle, check its specifications, save it to your wishlist, or send SBK a purchase inquiry.',
  priceNote: 'Final invoice can include freight, inspection and destination charges.',
  buyNote: 'SBK will confirm availability, export documents and shipping route before payment.',
  description: 'Contact SBK for full inspection notes, export documents, and shipment details for this vehicle.',
};

export const CHECKOUT_TEXT = {
  eyebrow: 'Secure Vehicle Request',
  title: 'Checkout your selected vehicles.',
  text: 'Submit your contact and destination details. SBK will confirm availability, total cost, export documents, and shipment options before payment.',
  formLabel: 'Buyer Details',
  formTitle: 'Where should SBK contact you?',
  minimumNote: 'For vehicles priced at $3,000 or below, the minimum payment is 25% even if 10% is selected.',
  summaryNote: 'Final invoice may include freight, inspection, documentation, and destination charges after SBK confirms your route.',
  doneLabel: 'Request Received',
  doneTitle: 'Thank you. SBK will contact you shortly.',
  doneText: 'Your checkout request has been saved. Our team will confirm vehicle availability, destination costs, and next payment steps.',
};

export const AUTH_TEXT = {
  login: {
    title: 'Customer Login',
    text: 'Access your SBK profile, saved details, and complete vehicle order records.',
    label: 'Account Access',
    heading: 'Login to your profile',
  },
  register: {
    title: 'Create Account',
    text: 'Register once and SBK will keep your profile and vehicle order history in one place.',
    label: 'New Customer',
    heading: 'Register your details',
  },
  forgot: {
    title: 'Set Your Password',
    text: 'Enter the email address your SBK account uses. We will email you a secure link to set a new password.',
    label: 'Account Access',
    heading: 'Forgot your password?',
  },
};

export const FOOTER_TEXT = 'SBK Auto Trading: Your trusted partner for premium vehicles worldwide, offering unparalleled selection and transparent service. Discover your dream car with confidence, backed by 15 years of global automotive trading expertise.';
