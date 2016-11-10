<?php
namespace Pristine\Mapper;

use Pristine\Enums\Features as Enum;

class Features {
	
	private $map
		= [
			"Adventure"                      => Enum::THEMES_ADVENTURE,
			"Aga Cooker"                     => Enum::KITCHEN_AGA_COOKER,
			"Air Conditioning"               => Enum::GENERAL_AIR_CONDITIONING,
			"Air Hockey"                     => Enum::ONSITE_AIR_HOCKEY,
			"Airport"                        => Enum::TRAVEL_AIRPORT,
			"Amazon Echo"                    => Enum::ENTERTAINMENT_AMAZON_ECHO,
			"American fridge"                => Enum::KITCHEN_AMERICAN_FRIDGE,
			"Antiqueing"                     => Enum::LOCAL_ANTIQUEING,
			"Archaeological sites"           => [Enum::ATTRACTIONS_ARCHAEOLOGICAL_SITES, Enum::LOCAL_ARCHAEOLOGICAL_SITES],
			"Armchairs"                      => Enum::LIVINGSPACE_ARMCHAIRS,
			"Badminton"                      => Enum::LOCAL_BADMINTON,
			"Badminton - Onsite"             => Enum::ONSITE_BADMINTON,
			"Balcony"                        => Enum::OUTDOOR_BALCONY,
			"Barbecue"                       => [Enum::LEISURE_BARBECUE, Enum::OUTDOOR_BARBECUE],
			"Baseball park"                  => [Enum::ATTRACTIONS_BASEBALL_PARK, Enum::LOCAL_BASEBALL_PARK],
			"Beach"                          => Enum::LOCAL_BEACH,
			"Beach - Onsite"                 => Enum::ONSITE_BEACH,
			"Beachcombing"                   => Enum::LOCAL_BEACHCOMBING,
			"Bed linen included"             => Enum::GENERAL_BED_LINEN_INCLUDED,
			"Bed linen not included"         => Enum::GENERAL_BED_LINEN_NOT_INCLUDED,
			"Bicycles"                       => Enum::ONSITE_BICYCLES,
			"Bike Rentals"                   => Enum::LOCAL_BIKE_RENTALS,
			"Bike Rentals - Onsite"          => Enum::ONSITE_BIKE_RENTALS,
			"Bird watching"                  => Enum::LOCAL_BIRD_WATCHING,
			"Bird Watching - Onsite"         => Enum::ONSITE_BIRD_WATCHING,
			"Blender"                        => Enum::KITCHEN_BLENDER,
			"Board Games"                    => Enum::ENTERTAINMENT_BOARD_GAMES,
			"Boat Mooring"                   => Enum::LOCAL_BOAT_MOORING,
			"Boat Mooring - Onsite"          => Enum::OUTDOOR_BOAT_MOORING,
			"Boat Rentals/Charters"          => Enum::LOCAL_BOAT_RENTALS,
			"Boat Rentals"                   => Enum::LOCAL_BOAT_RENTALS,
			"Boat Rentals/Charters - Onsite" => Enum::ONSITE_BOAT_RENTALS,
			"Boat Slip/Dock"                 => Enum::OUTDOOR_BOAT_SLIP_DOCK,
			"Body Board"                     => Enum::ONSITE_BODY_BOARD,
			"Books Library"                  => Enum::ENTERTAINMENT_BOOKS_LIBRARY,
			"Botanical gardens"              => Enum::LOCAL_BOTANICAL_GARDENS,
			"Botanical gardens - Onsite"     => Enum::ONSITE_BOTANICAL_GARDENS,
			"Bowls green"                    => Enum::LOCAL_BOWLS_GREEN,
			"Bowls green - Onsite"           => Enum::ONSITE_BOWLS_GREEN,
			"Breakfast Available"            => Enum::GENERAL_BREAKFAST_AVAILABLE,
			"Budget"                         => Enum::THEMES_BUDGET,
			"Building has Doorman"           => Enum::GENERAL_BUILDING_HAS_DOORMAN,
			"Cable car"                      => Enum::LOCAL_CABLE_CAR,
			"Cable TV"                       => Enum::ENTERTAINMENT_CABLE_TV,
			"Cafetiere"                      => Enum::KITCHEN_CAFETIERE,
			"Canoe/Kayak"                    => Enum::LOCAL_CANOE_KAYAK,
			"Canoe/Kayak - Onsite"           => Enum::ONSITE_CANOE_KAYAK,
			"Canoeing/Kayaking"              => [Enum::LOCAL_CANOEING_KAYAKING, Enum::SPORTS_KAYAK_SUPPLIED],
			"Canoeing/Kayaking - Onsite"     => Enum::ONSITE_KAYAK_SUPPLIED,
			"Car (Provided)"                 => [Enum::ONSITE_CAR_SUPPLIED, Enum::TRAVEL_CAR_SUPPLIED],
			"Car essential"                  => Enum::TRAVEL_CAR_ESSENTIAL,
			"Car not necessary"              => Enum::TRAVEL_CAR_NOT_NECESSARY,
			"Car recommended"                => Enum::TRAVEL_CAR_RECOMMENDED,
			"Casino"                         => [Enum::ATTRACTIONS_CASINO, Enum::LOCAL_CASINO],
			"Casino - Onsite"                => Enum::ONSITE_CASINO,
			"Catered"                        => Enum::GENERAL_CATERED,
			"Ceiling Fan"                    => Enum::GENERAL_CEILING_FAN,
			"Celing Fan"                     => Enum::LIVINGSPACE_CEILING_FAN,
			"Central Heating"                => Enum::GENERAL_CENTRAL_HEATING,
			"Chaise Longue"                  => Enum::LIVINGSPACE_CHAISE_LONGUE,
			"Children's Playground"          => Enum::LOCAL_CHILDRENS_PLAYGROUND,
			"Children's Playground - Onsite" => Enum::ONSITE_CHILDRENS_PLAYGROUND,
			"Children's Toys"                => Enum::ENTERTAINMENT_CHILDRENS_TOYS,
			"Church"                         => Enum::LOCAL_CHURCH,
			"Synagogue"                      => Enum::LOCAL_SYNAGOGUE,
			"Temple"                         => Enum::LOCAL_TEMPLE,
			"Cinema"                         => Enum::LOCAL_CINEMA,
			"Cinema - Onsite"                => Enum::ONSITE_CINEMA,
			"City Centre"                    => [Enum::LOCALITY_CITY_CENTRE, Enum::SITUATION_CITY_CENTRE],
			"City Skyline Views"             => Enum::LOCALITY_CITY_SKYLINE_VIEWS,
			"City"                           => [Enum::LOCALITY_CITY, Enum::SITUATION_CITY],
			
			"Climbing"           => [Enum::LOCAL_CLIMBING, Enum::SPORTS_CLIMBING],
			"Climbing - Onsite"  => [Enum::ONSITE_CLIMBING, Enum::SPORTS_CLIMBING],
			"Close to Nightlife" => Enum::LOCALITY_CLOSE_TO_NIGHTLIFE,
			"Close to Skiing"    => Enum::LOCALITY_CLOSE_TO_SKIING,
			"Clothes Horse"      => Enum::GENERAL_CLOTHES_HORSE,
			"Coastal"            => [Enum::LOCALITY_COASTAL, Enum::SITUATION_COASTAL],
			
			"Coffee Grinder"                  => Enum::KITCHEN_COFFEE_GRINDER,
			"Coffee Machine"                  => Enum::KITCHEN_COFFEE_MACHINE,
			"Coffee Percolator"               => Enum::KITCHEN_COFFEE_PERCOLATOR,
			"Coffee Tables"                   => Enum::LIVINGSPACE_COFFEE_TABLES,
			"Computer Available"              => Enum::COMMUNICATION_COMPUTER_AVAILABLE,
			"Concert hall"                    => Enum::LOCAL_CONCERT_HALL,
			"Concierge Service"               => Enum::GENERAL_CONCIERGE_SERVICE,
			"Conservation area"               => Enum::LOCAL_CONSERVATION_AREA,
			"Cook"                            => Enum::GENERAL_COOK_AVAILABLE,
			"Cook - Included"                 => Enum::GENERAL_COOK_INCLUDED,
			"Corporate"                       => Enum::THEMES_CORPORATE,
			"Cot (Provided)"                  => Enum::GENERAL_COT_SUPPLIED,
			"Cot Available"                   => Enum::GENERAL_COT_AVAILABLE,
			"Cot to Rent"                     => Enum::GENERAL_COT_TO_RENT,
			"Countryside"                     => [Enum::LOCALITY_COUNTRYSIDE, Enum::SITUATION_COUNTRYSIDE],
			"Courtyard"                       => Enum::GENERAL_COURTYARD,
			"Creche"                          => Enum::ONSITE_CRECHE,
			"Cross country skiing"            => [Enum::ONSITE_CROSS_COUNTRY_SKIING, Enum::SPORTS_CROSS_COUNTRY_SKIING],
			"Curling"                         => Enum::LOCAL_CURLING,
			"Deck"                            => Enum::OUTDOOR_DECK,
			"Digital TV"                      => Enum::ENTERTAINMENT_DIGITAL_TV,
			"Dining Room"                     => Enum::GENERAL_DINING_ROOM,
			"Dining Table & Chairs"           => Enum::LIVINGSPACE_DINING_TABLE_CHAIRS,
			"Dishwasher"                      => Enum::KITCHEN_DISHWASHER,
			"Dog Pen/Kennel"                  => Enum::OUTDOOR_DOG_PEN_KENNEL,
			"DVD Player"                      => Enum::ENTERTAINMENT_DVD_PLAYER,
			"Eco Friendly"                    => Enum::GENERAL_ECO_FRIENDLY,
			"Electric Hob"                    => Enum::KITCHEN_ELECTRIC_HOB,
			"Electric Kettle"                 => Enum::KITCHEN_ELECTRIC_KETTLE,
			"Electric Radiators"              => Enum::GENERAL_ELECTRIC_RADIATORS,
			"Exclusive"                       => Enum::THEMES_EXCLUSIVE,
			"Explorer"                        => Enum::THEMES_EXPLORER,
			"Extra Bed (On Request)"          => Enum::GENERAL_EXTRA_BED_AVAILABLE,
			"Family Fun"                      => Enum::THEMES_FAMILY_FUN,
			"Fax Machine"                     => Enum::COMMUNICATION_FAX_MACHINE,
			"Fenced Perimeter"                => Enum::SAFETY_FENCED_PERIMETER,
			"Ferry"                           => Enum::TRAVEL_FERRY,
			"Fireplace (Coal/Wood)"           => Enum::LIVINGSPACE_FIREPLACE_COAL_WOOD,
			"Fireplace (Gas/Electric)"        => Enum::LIVINGSPACE_FIREPLACE_GAS_ELECTRIC,
			"Fireplace"                       => Enum::GENERAL_FIREPLACE,
			"Firewood Supplied"               => Enum::GENERAL_FIREWOOD_SUPPLIED,
			"Fishing Gear"                    => Enum::ONSITE_FISHING_GEAR,
			"Fishing"                         => Enum::SPORTS_FISHING,
			"Fly Fishing"                     => Enum::SPORTS_FISHING_FLY,
			"Freshwater Fishing"              => Enum::SPORTS_FISHING_FRESHWATER,
			"Float Plane/Helicopter"          => Enum::LEISURE_FLOAT_PLANE_HELICOPTER,
			"Float Plane/Helicopter - Onsite" => Enum::ONSITE_FLOAT_PLANE_HELICOPTER,
			"Flooring - Carpet"               => Enum::LIVINGSPACE_FLOORING_CARPET,
			"Flooring - Other"                => Enum::LIVINGSPACE_FLOORING_OTHER,
			"Flooring - Stone"                => Enum::LIVINGSPACE_FLOORING_STONE,
			"Flooring - Wood"                 => Enum::LIVINGSPACE_FLOORING_WOOD,
			"Food Processor"                  => Enum::KITCHEN_FOOD_PROCESSOR,
			"Foosball/Table Football"         => Enum::ENTERTAINMENT_FOOSBALL,
			"Football stadium"                => Enum::LOCAL_FOOTBALL_STADIUM,
			"For Sale"                        => Enum::THEMES_FOR_SALE,
			"Forest"                          => [Enum::LOCALITY_FOREST, Enum::SITUATION_FOREST],
			"Freezer (Standalone)"            => Enum::KITCHEN_FREEZER_STANDALONE,
			"Fridge (Standalone)"             => Enum::KITCHEN_FRIDGE_STANDALONE,
			"Fridge Freezer"                  => Enum::KITCHEN_FRIDGE_FREEZER,
			"Front Desk"                      => Enum::GENERAL_FRONT_DESK,
			"Full Crockery Set"               => Enum::KITCHEN_FULL_CROCKERY_SET,
			"Full Cutlery/Utensil Kit"        => Enum::KITCHEN_FULL_CUTLERY_UTENSIL_KIT,
			"Full Gym"                        => Enum::SPA_POOL_FULL_GYM,
			"Funicular Railway"               => Enum::LOCAL_FUNICULAR_RAILWAY,
			"Galleries"                       => Enum::LOCAL_GALLERIES,
			"Games Console"                   => Enum::ENTERTAINMENT_GAMES_CONSOLE,
			"Games Room"                      => Enum::ENTERTAINMENT_GAMES_ROOM,
			"Games Room - Onsite"             => Enum::ONSITE_GAMES_ROOM,
			"Garage"                          => Enum::OUTDOOR_GARAGE,
			"Garden shared"                   => Enum::GENERAL_GARDEN_SHARED,
			"Garden"                          => Enum::OUTDOOR_GARDEN,
			"Gas Hob"                         => Enum::KITCHEN_GAS_HOB,
			"Gay friendly"                    => Enum::THEMES_GAY_FRIENDLY,
			"Gay owner"                       => Enum::THEMES_GAY_OWNER,
			"Gazebo"                          => Enum::OUTDOOR_GAZEBO,
			"Go-karting"                      => Enum::LOCAL_GO_KARTING,
			"Golf - Full Bagged Set"          => Enum::ONSITE_GOLF_FULL_BAGGED_SET,
			"Golf Cart"                       => Enum::ONSITE_GOLF_CART,
			"Golf Resort"                     => Enum::LOCALITY_GOLF_COURSE,
			"Golf"                            => [Enum::LOCAL_GOLF, Enum::THEMES_GOLF, Enum::SPORTS_GOLF],
			"Golf - Onsite"                   => [Enum::ONSITE_GOLF, Enum::SPORTS_GOLF],
			
			"Google Home"                       => Enum::ENTERTAINMENT_GOOGLE_HOME,
			"Griddle Grill"                     => Enum::KITCHEN_GRIDDLE_GRILL,
			"Grocery shopping nearby"           => Enum::LOCAL_GROCERY_SHOPPING_NEARBY,
			"Grocery shopping - Onsite"         => Enum::ONSITE_GROCERY_SHOPPING_NEARBY,
			"Ground floor bathroom"             => Enum::GENERAL_GROUND_FLOOR_BATHROOM,
			"Ground floor bedroom & facilities" => Enum::GENERAL_GROUND_FLOOR_BEDROOM_FACILITIES,
			"Ground floor shower room"          => Enum::GENERAL_GROUND_FLOOR_SHOWER_ROOM,
			"Ground floor WC"                   => Enum::GENERAL_GROUND_FLOOR_WC,
			"Gym Equipment"                     => Enum::SPA_POOL_GYM_EQUIPMENT,
			"Gym"                               => Enum::LOCAL_GYM,
			"Gym - Onsite"                      => Enum::ONSITE_GYM,
			"Gym - Outdoor"                     => Enum::OUTDOOR_GYM,
			"Hairdryer"                         => Enum::GENERAL_HAIRDRYER,
			"Hammock"                           => [Enum::LEISURE_HAMMOCK, Enum::OUTDOOR_HAMMOCK],
			
			"Health Facilities"    => Enum::SPA_POOL_HEALTH_FACILITIES,
			"Health"               => Enum::THEMES_HEALTH,
			"Heated towel rail"    => Enum::GENERAL_HEATED_TOWEL_RAIL,
			"Helipad"              => Enum::GENERAL_HELIPAD,
			"Hens"                 => Enum::THEMES_HENS,
			"High Definition TV"   => Enum::ENTERTAINMENT_HIGH_DEFINITION_TV,
			"Hiking"               => [Enum::LEISURE_HIKING, Enum::SPORTS_HIKING],
			"Hiking/Trails"        => Enum::LEISURE_HIKING,
			"Historic"             => Enum::THEMES_HISTORIC,
			"Historical Monuments" => [Enum::ATTRACTIONS_HISTORICAL_MONUMENTS, Enum::LOCAL_HISTORICAL_MONUMENTS],
			
			"Historical Ruins" => [Enum::LOCAL_HISTORICAL_RUINS, Enum::ATTRACTIONS_HISTORICAL_RUINS],
			
			"Home Cinema"                    => Enum::ENTERTAINMENT_HOME_CINEMA,
			"Horseback Riding"               => [Enum::LEISURE_HORSEBACK_RIDING, Enum::SPORTS_HORSEBACK_RIDING],
			"Hot air balloon rides"          => Enum::LOCAL_HOT_AIR_BALLOON_RIDES,
			"Hot air balloon rides - Onsite" => Enum::ONSITE_HOT_AIR_BALLOON_RIDES,
			"Hot springs"                    => Enum::LOCAL_HOT_SPRINGS,
			"Ice climbing"                   => [Enum::LOCAL_ICE_CLIMBING, Enum::SPORTS_ICE_CLIMBING],
			
			"Ice hockey"                          => Enum::LOCAL_ICE_HOCKEY,
			"Ice Rink"                            => Enum::LOCAL_ICE_RINK,
			"Ice Rink - Onsite"                   => Enum::ONSITE_ICE_RINK,
			"Ice Skating - Onsite"                => Enum::ONSITE_ICE_SKATING,
			"Ice Skating"                         => Enum::SPORTS_ICE_SKATING,
			"Icemaker"                            => Enum::KITCHEN_ICEMAKER,
			"Induction Hob"                       => Enum::KITCHEN_INDUCTION_HOB,
			"Inflatable Dinghy"                   => Enum::ONSITE_INFLATABLE_DINGHY,
			"Internet - Cabled"                   => Enum::COMMUNICATION_INTERNET_CABLED,
			"Internet - Dial-up"                  => Enum::COMMUNICATION_INTERNET_DIAL_UP,
			"Internet - Dongle"                   => Enum::COMMUNICATION_INTERNET_DONGLE,
			"Internet - Highspeed"                => Enum::COMMUNICATION_INTERNET_HIGHSPEED,
			"Internet - Wi-Fi"                    => Enum::COMMUNICATION_INTERNET_WIFI,
			"iPod Docking Station"                => Enum::ENTERTAINMENT_IPOD_DOCKING,
			"Iron/Ironing Board"                  => Enum::GENERAL_IRON_IRONING_BOARD,
			"Jacuzzi/Hot Tub"                     => Enum::SPA_POOL_JACUZZI_HOT_TUB,
			"Jetski/Personal Watercraft"          => Enum::LOCAL_JETSKI_PERSONAL_WATERCRAFT,
			"Jetski/Personal Watercraft - Onsite" => Enum::ONSITE_JETSKI_PERSONAL_WATERCRAFT,
			"Juicer"                              => Enum::KITCHEN_JUICER,
			"Keyboard"                            => Enum::ENTERTAINMENT_MUSIC_KEYBOARD,
			"Kids Toys"                           => Enum::ENTERTAINMENT_KIDS_TOYS,
			"Kitchen (Full)"                      => Enum::KITCHEN_KITCHEN_FULL,
			"Kitchen Diner"                       => Enum::KITCHEN_KITCHEN_DINER,
			"Kitchenette"                         => Enum::KITCHEN_KITCHENETTE,
			"Lake Views"                          => Enum::LOCALITY_LAKE_VIEWS,
			"Lakeside Town"                       => [Enum::LOCALITY_LAKESIDE_TOWN, Enum::SITUATION_LAKESIDE_TOWN],
			
			"Lanai"                         => Enum::OUTDOOR_LANAI,
			"Large Groups"                  => Enum::THEMES_LARGE_GROUPS,
			"Laundry Facilities"            => Enum::GENERAL_LAUNDRY_FACILITIES,
			"Library"                       => Enum::LOCAL_LIBRARY,
			"Library - Onsite"              => Enum::ONSITE_LIBRARY,
			"Lift/Elevator"                 => Enum::GENERAL_ELEVATOR,
			"Lighthouse"                    => Enum::LOCAL_LIGHTHOUSE,
			"Local Park/Green Space"        => Enum::LOCAL_PARK_GREEN_SPACE,
			"Loch Views"                    => Enum::LOCALITY_LOCH_VIEWS,
			"Long Stay"                     => Enum::THEMES_LONG_STAY,
			"Lounge/Diner"                  => Enum::GENERAL_LOUNGE_DINER,
			"Luxury"                        => Enum::THEMES_LUXURY,
			"Markets"                       => Enum::LOCAL_MARKETS,
			"Massage"                       => Enum::SPA_POOL_MASSAGE,
			"Metered Air Conditioning"      => Enum::GENERAL_METERED_AIR_CONDITIONING,
			"Metered Electricity"           => Enum::GENERAL_METERED_ELECTRICITY,
			"Microwave"                     => Enum::KITCHEN_MICROWAVE,
			"Miniature golf"                => Enum::LOCAL_MINIATURE_GOLF,
			"Miniature golf - Onsite"       => Enum::ONSITE_MINIATURE_GOLF,
			"Monthly Rental"                => Enum::THEMES_MONTHLY_RENTAL,
			"Moorland"                      => [Enum::LOCALITY_MOORLAND, Enum::SITUATION_MOORLAND],
			"Mountain biking"               => Enum::LOCAL_MOUNTAIN_BIKING,
			"Mountain biking - Onsite"      => Enum::ONSITE_MOUNTAIN_BIKING,
			"Mountain Retreat"              => [Enum::LOCALITY_MOUNTAIN_RETREAT, Enum::SITUATION_MOUNTAIN_RETREAT],
			"Mountain Views"                => Enum::LOCALITY_MOUNTAIN_VIEWS,
			"Movie theatre"                 => Enum::LOCAL_MOVIE_THEATRE,
			"Movie theatre - Onsite"        => Enum::ONSITE_MOVIE_THEATRE,
			"Movie/Video Library"           => Enum::ENTERTAINMENT_MOVIE_VIDEO_LIBRARY,
			"Museum"                        => [Enum::ATTRACTIONS_MUSEUM, Enum::LOCAL_MUSEUM],
			"Music Library"                 => Enum::ENTERTAINMENT_MUSIC_LIBRARY,
			"National Heritage"             => [Enum::ATTRACTIONS_NATIONAL_HERITAGE, Enum::LOCAL_NATIONAL_HERITAGE],
			"National Park"                 => [Enum::ATTRACTIONS_NATIONAL_PARK, Enum::LOCAL_NATIONAL_PARK],
			"National Trust properties"     => Enum::LOCAL_NATIONAL_TRUST_PROPERTIES,
			"Nature reserve"                => Enum::LOCAL_NATURE_RESERVE,
			"Nature reserve - Onsite"       => Enum::ONSITE_NATURE_RESERVE,
			"Nightlife"                     => Enum::THEMES_NIGHTLIFE,
			"Ocean Views"                   => Enum::LOCALITY_OCEAN_VIEWS,
			"On Site Staff"                 => Enum::GENERAL_ON_SITE_STAFF,
			"Outdoor Dining"                => Enum::OUTDOOR_OUTDOOR_DINING,
			"Outdoor shower"                => Enum::OUTDOOR_OUTDOOR_SHOWER,
			"Outdoor Towels (Not Provided)" => Enum::SPA_POOL_OUTDOOR_TOWELS_NOT_SUPPLIED,
			"Outdoor Towels (Provided)"     => Enum::SPA_POOL_OUTDOOR_TOWELS_SUPPLIED,
			"Outside Stone Pizza/Oven"      => Enum::OUTDOOR_OUTSIDE_STONE_PIZZA_OVEN,
			"Oven Grill"                    => Enum::KITCHEN_OVEN_GRILL,
			"Oven"                          => Enum::KITCHEN_OVEN,
			"Paddle boarding"               => Enum::LOCAL_PADDLE_BOARDING,
			"Paddle boarding - Onsite"      => Enum::ONSITE_PADDLE_BOARDING,
			"Paper Towels"                  => Enum::KITCHEN_PAPER_TOWELS,
			"Parasailing"                   => [Enum::LOCAL_PARASAILING, Enum::SPORTS_PARASAILING],
			"Parasailing - Onsite"          => [Enum::ONSITE_PARASAILING, Enum::SPORTS_PARASAILING],
			
			"Park"                            => Enum::LOCAL_PARK,
			"Parking Space"                   => Enum::GENERAL_PARKING_SPACE,
			"Patio"                           => Enum::OUTDOOR_PATIO,
			"Pergola"                         => Enum::OUTDOOR_PERGOLA,
			"Personal welcome/Meet and Greet" => Enum::GENERAL_PERSONAL_WELCOME,
			"Pet friendly"                    => Enum::GENERAL_PET_FRIENDLY,
			"Piano"                           => Enum::ENTERTAINMENT_MUSIC_PIANO,
			"Planetarium"                     => [Enum::ATTRACTIONS_PLANETARIUM, Enum::LOCAL_PLANETARIUM],
			"Pool (Communal)"                 => Enum::SPA_POOL_POOL_COMMUNAL,
			"Pool (Indoor)"                   => Enum::SPA_POOL_POOL_INDOOR,
			"Pool (infant)"                   => Enum::SPA_POOL_POOL_INFANT,
			"Pool (Private)"                  => Enum::SPA_POOL_POOL_PRIVATE,
			"Pool Heated"                     => Enum::SPA_POOL_POOL_HEATED,
			"Pool Table"                      => Enum::ENTERTAINMENT_POOL_TABLE,
			"Pool Table - Onsite"             => Enum::ONSITE_POOL_TABLE,
			"Pool Unheated"                   => Enum::SPA_POOL_POOL_UNHEATED,
			"Portable Fan"                    => Enum::GENERAL_PORTABLE_FAN,
			"Private Parking"                 => Enum::LOCALITY_PRIVATE_PARKING,
			"Private Parking - Onsite"        => Enum::OUTDOOR_PRIVATE_PARKING,
			"Promotional"                     => Enum::THEMES_PROMOTIONAL,
			"Public Transportation"           => Enum::TRAVEL_PUBLIC_TRANSPORTATION,
			"Public Villas & Gardens"         => [Enum::ATTRACTIONS_PUBLIC_VILLAS_GARDENS, Enum::LOCAL_PUBLIC_VILLAS_GARDENS],
			"Quad Bike"                       => Enum::ONSITE_QUAD_BIKE,
			"Radio"                           => Enum::ENTERTAINMENT_RADIO,
			"Range Cooker"                    => Enum::KITCHEN_RANGE_COOKER,
			"Recliners"                       => Enum::LIVINGSPACE_RECLINERS,
			"Religious"                       => Enum::LOCAL_RELIGIOUS_CENTRE,
			"River Views"                     => Enum::LOCALITY_RIVER_VIEWS,
			"Romance"                         => Enum::THEMES_ROMANCE,
			"Rowing Boats"                    => Enum::LOCAL_ROWING_BOATS,
			"Rowing Boats - Onsite"           => Enum::ONSITE_ROWING_BOATS,
			"Rural"                           => [Enum::LOCALITY_RURAL, Enum::SITUATION_RURAL],
			"Sailing Dinghy"                  => Enum::LOCAL_SAILING_DINGHY,
			"Sailing Dinghy - Onsite"         => Enum::ONSITE_SAILING_DINGHY,
			"Sailing"                         => [Enum::LOCAL_SAILING, Enum::SPORTS_SAILING],
			"Sailing - Onsite"                => Enum::ONSITE_SAILING,
			"Same Sex Groups"                 => Enum::THEMES_SAME_SEX_GROUPS,
			"Satellite TV"                    => Enum::ENTERTAINMENT_SATELLITE_TV,
			"Sauna"                           => Enum::SPA_POOL_SAUNA,
			"Scuba Diving - Onsite"           => Enum::ONSITE_SCUBA_DIVING,
			"Scuba Diving"                    => Enum::SPORTS_SCUBA_DIVING,
			"Sea, Sand, Sun"                  => Enum::THEMES_SEA_SAND_SUN,
			"Sea/Waterfront"                  => Enum::LOCALITY_SEA_WATERFRONT,
			"Seaside Town"                    => [Enum::LOCALITY_SEASIDE_TOWN, Enum::SITUATION_SEASIDE_TOWN],
			"Security Alarm/System"           => Enum::SAFETY_SECURITY_ALARM_SYSTEM,
			"Security Safe"                   => Enum::GENERAL_SECURITY_SAFE,
			"Shopping"                        => Enum::THEMES_SHOPPING,
			"Short Stay"                      => Enum::THEMES_SHORT_STAY,
			"Ski boot heater"                 => Enum::ONSITE_SKI_BOOT_HEATER,
			"Ski In/Ski Out"                  => Enum::LOCALITY_SKI_IN_SKI_OUT,
			"Ski Shuttle"                     => Enum::TRAVEL_SKI_SHUTTLE,
			"Ski Storage"                     => Enum::GENERAL_SKI_STORAGE,
			"Skidoo"                          => Enum::LOCAL_SKIDOO,
			"Skidoo - Onsite"                 => Enum::ONSITE_SKIDOO,
			"Skidoo/Snow Mobile"              => [Enum::LOCAL_SKIDOO_SNOW_MOBILE, Enum::SPORTS_SKIDOO_SNOW_MOBILE],
			"Skidoo/Snow Mobile - Onsite"     => [Enum::ONSITE_SKIDOO_SNOW_MOBILE, Enum::SPORTS_SKIDOO_SNOW_MOBILE],
			
			"Skiing - Snow"  => [Enum::LOCAL_SKIING, Enum::SPORTS_SKIING, Enum::THEMES_SKIING],
			"Skiing - Water" => [Enum::LOCAL_WATER_SKIING, Enum::SPORTS_WATER_SKIING, Enum::THEMES_SKIING],
			"Skiing"         => Enum::THEMES_SKIING,
			"Snooker Table"  => Enum::ENTERTAINMENT_SNOOKER_TABLE,
			"Snorkelling"    => Enum::LOCAL_SNORKELLING,
			"Snow boarding"  => Enum::LOCAL_SNOW_BOARDING,
			"Snow Boarding"  => [Enum::LOCAL_SNOW_BOARDING, Enum::SPORTS_SNOW_BOARDING],
			
			"Soap/Shampoo (Provided)"      => Enum::GENERAL_SOAP_SHAMPOO_SUPPLIED,
			"Sofa Pouffe"                  => Enum::LIVINGSPACE_SOFA_POUFFE,
			"Sofas/Couches"                => Enum::LIVINGSPACE_SOFAS_COUCHES,
			"Solar heating"                => Enum::GENERAL_SOLAR_HEATING,
			"Solarium"                     => Enum::LEISURE_SOLARIUM,
			"Solarium - Onsite"            => Enum::OUTDOOR_SOLARIUM,
			"Spa Wellness Centre"          => Enum::LOCAL_SPA_WELLNESS_CENTRE,
			"Spa Wellness Centre - Onsite" => Enum::ONSITE_SPA_WELLNESS_CENTRE,
			"Spa"                          => Enum::THEMES_SPA,
			"Squash - Onsite"              => Enum::ONSITE_SQUASH,
			"Squash"                       => Enum::SPORTS_SQUASH,
			"Stags"                        => Enum::THEMES_STAGS,
			"Stair Gates"                  => Enum::SAFETY_STAIR_GATES,
			"Standard Definition TV"       => Enum::ENTERTAINMENT_STANDARD_DEFINITION_TV,
			"Steam Shower"                 => Enum::SPA_POOL_STEAM_SHOWER,
			"Stereo System"                => Enum::ENTERTAINMENT_STEREO_SYSTEM,
			"Stone/Pizza Oven"             => Enum::KITCHEN_STONE_PIZZA_OVEN,
			"Storage Heaters"              => Enum::GENERAL_STORAGE_HEATERS,
			"Suitable for elderly"         => Enum::GENERAL_SUITABLE_FOR_ELDERLY,
			"Summer House"                 => Enum::OUTDOOR_SUMMER_HOUSE,
			"Sun Loungers"                 => Enum::SPA_POOL_SUN_LOUNGERS,
			"Surf Board"                   => Enum::ONSITE_SURF_BOARD,
			"Surfing"                      => [Enum::LOCAL_SURFING, Enum::SPORTS_SURFING],
			"Swimming Pool"                => Enum::SPA_POOL_SWIMMING_POOL,
			"Swing Set"                    => Enum::OUTDOOR_SWING_SET,
			"Table and Chairs"             => Enum::OUTDOOR_TABLE_AND_CHAIRS,
			"Table Tennis"                 => Enum::ENTERTAINMENT_TABLE_TENNIS,
			"Table tennis"                 => Enum::LOCAL_TABLE_TENNIS,
			"Table tennis - Onsite"        => Enum::ONSITE_TABLE_TENNIS,
			"Taxis"                        => Enum::TRAVEL_TAXIS,
			"Telephone"                    => Enum::COMMUNICATION_TELEPHONE,
			"Tennis Court - Onsite"        => Enum::ONSITE_TENNIS_COURT,
			"Tennis Court"                 => Enum::SPORTS_TENNIS_COURT,
			"Tenpin bowling"               => Enum::LOCAL_TENPIN_BOWLING,
			"Tenpin bowling - Onsite"      => Enum::ONSITE_TENPIN_BOWLING,
			"Terrace"                      => Enum::OUTDOOR_TERRACE,
			"Theatre"                      => Enum::LOCAL_THEATRE,
			"Theme Park"                   => Enum::ATTRACTIONS_THEME_PARK,
			"Toaster"                      => Enum::KITCHEN_TOASTER,
			"Tobogganing"                  => Enum::LOCAL_TOBOGGANING,
			"Tobogganing - Onsite"         => Enum::ONSITE_TOBOGGANING,
			"Town Centre"                  => [Enum::LOCALITY_TOWN_CENTRE, Enum::SITUATION_TOWN_CENTRE],
			
			"Town"                  => [Enum::SITUATION_TOWN, Enum::LOCALITY_TOWN],
			"Train Station"         => Enum::TRAVEL_TRAIN_STATION,
			"Trams"                 => Enum::TRAVEL_TRAMS,
			"Tropical"              => [Enum::SITUATION_TROPICAL, Enum::THEMES_TROPICAL],
			"Tumble Dryer"          => Enum::GENERAL_TUMBLE_DRYER,
			"Under Floor Heating"   => Enum::GENERAL_UNDER_FLOOR_HEATING,
			"Universal shaver plug" => Enum::GENERAL_UNIVERSAL_SHAVER_PLUG,
			"Utility room"          => Enum::GENERAL_UTILITY_ROOM,
			"Valley Views"          => Enum::LOCALITY_VALLEY_VIEWS,
			"VCR Player"            => Enum::ENTERTAINMENT_VCR_PLAYER,
			"Video Game Library"    => Enum::ENTERTAINMENT_VIDEO_GAME_LIBRARY,
			"Village"               => [Enum::LOCALITY_VILLAGE, Enum::SITUATION_VILLAGE],
			"Waffle Maker"          => Enum::KITCHEN_WAFFLE_MAKER,
			"Washer/Dryer"          => Enum::GENERAL_WASHER_DRYER,
			"Washing Machine"       => Enum::GENERAL_WASHING_MACHINE,
			"Waste Disposal"        => Enum::KITCHEN_WASTE_DISPOSAL,
			"Water Cooler"          => Enum::KITCHEN_WATER_COOLER,
			"Water Skis"            => Enum::ONSITE_WATER_SKIS,
			"Water Taxi"            => Enum::TRAVEL_WATER_TAXI,
			"Water Theme Park"      => [Enum::ATTRACTIONS_WATER_THEME_PARK, Enum::LOCAL_WATER_THEME_PARK],
			"Water Views"           => Enum::LOCALITY_WATER_VIEWS,
			"Waterfalls"            => [Enum::ATTRACTIONS_WATERFALLS, Enum::LOCAL_WATERFALLS],
			
			"Wellness centre"          => Enum::LOCAL_WELLNESS_CENTRE,
			"Wellness centre - Onsite" => Enum::ONSITE_WELLNESS_CENTRE,
			"Whale watching"           => Enum::LOCAL_WHALE_WATCHING,
			"Whirlpool tub"            => Enum::SPA_WHIRLPOOL_TUB,
			"Whirlpool"                => Enum::SPA_POOL_WHIRLPOOL,
			"White water rafting"      => Enum::LOCAL_WHITE_WATER_RAFTING,
			"Wilderness"               => Enum::THEMES_WILDERNESS,
			"Wildlife centre"          => Enum::LOCAL_WILDLIFE_CENTRE,
			"Wildlife viewing"         => Enum::LOCAL_WILDLIFE_VIEWING,
			"Wind surfing"             => [Enum::LOCAL_WIND_SURFING, Enum::SPORTS_WIND_SURFING],
			"Wind surfing - Onsite"    => Enum::ONSITE_WIND_SURFING,
			"Wine Cooler"              => Enum::KITCHEN_WINE_COOLER,
			"Within National Park"     => Enum::LOCALITY_WITHIN_NATIONAL_PARK,
			"Wood Burning Stove"       => Enum::LIVINGSPACE_WOOD_BURNING_STOVE,
			"Wooded Surroundings"      => Enum::LOCALITY_WOODED_SURROUNDINGS,
			"Zoo"                      => [Enum::ATTRACTIONS_ZOO, Enum::LOCAL_ZOO]
		
		];
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		return $this->map;
	}
}