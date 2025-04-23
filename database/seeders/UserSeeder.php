<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        User::create([
            'fname' => 'Chan',
            'lname' => 'Amistad',
            'email' => 'admin@gmail.com',
            'phone_number' => '+639011129219',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'admin',
            'password' => Hash::make('Admin123'),
        ]);

        // Environmental admin
        User::create([
            'fname' => 'Environmental',
            'lname' => 'Admin',
            'email' => 'Environment@gmail.com',
            'phone_number' => '1234591827',
            'city' => 'Olongapo',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'agri',
            'password' => Hash::make('EnviAd123'),
        ]);

        // Sk admin
        User::create([
            'fname' => 'Sk',
            'lname' => 'Admin',
            'email' => 'SkAdmin@gmail.com',
            'phone_number' => '1234591827',
            'city' => 'Olongapo',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'sangukab',
            'password' => Hash::make('SkAdmin123'),
        ]);

        // Create 30 users
        User::create([
            'fname' => 'Czarina',
            'lname' => 'Arellano',
            'email' => 'czarinaarellano12@gmail.com',
            'phone_number' => '+63 967-210-7127',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2003-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('Arellano12!'),
        ]);

        User::create([
            'fname' => 'CJ',
            'lname' => 'Arellano',
            'email' => 'zarinajeyn@gmail.com',
            'phone_number' => '+63 967-210-7127',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2003-02-05',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('Arellano123!'),
        ]);

        User::create([
            'fname' => 'John Mark',
            'lname' => 'Pintol',
            'email' => 'johnmarkpintol031@gmail.com',
            'phone_number' => '+63 901-112-9219',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2002-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Jane Marie',
            'lname' => 'Tinupolo',
            'email' => 'janemarieganda21@gmail.com',
            'phone_number' => '+63 993-105-8213',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Woodhouse St. Gordon Heights',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Alexander',
            'lname' => 'Yumol',
            'email' => 'Alexanderyumol034@gmail.com',
            'phone_number' => '+63 998-239-1832',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Acacia St. Gordon Heights',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'John Andrei',
            'lname' => 'Velasco',
            'email' => 'Johnandreivelasco@gmail.com',
            'phone_number' => '+63 973-435-7876',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #15 Latiris St. Gordon Heights',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'John Mark',
            'lname' => 'Pikero',
            'email' => 'Johnmarkpikero12@gmail.com',
            'phone_number' => '+63 982-936-28192',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #25 Waling Waling St. Gordon Heights',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Jay-Vee',
            'lname' => 'Ubaldo',
            'email' => 'jayveeubaldo05@gmail.com',
            'phone_number' => '+63 998-366-6893',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #21 Santol St. Gordon Heights',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('123123123'),
        ]);
        
        User::create([
            'fname' => 'Maria Clara',
            'lname' => 'Santos',
            'email' => 'mariaclarasantos@gmail.com',
            'phone_number' => '+63 917-822-3456',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #12 Ipil-Ipil St. (Long Rd, Upper)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Antonio',
            'lname' => 'Dela Cruz',
            'email' => 'antonio.delacruz@gmail.com',
            'phone_number' => '+63 927-688-4512',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #3 Diwa St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Liza',
            'lname' => 'Mercado',
            'email' => 'lizamercado2025@gmail.com',
            'phone_number' => '+63 972-233-4578',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #15 Latiris St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Carlos',
            'lname' => 'Mendoza',
            'email' => 'carlosmendoza33@gmail.com',
            'phone_number' => '+63 912-345-6789',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #19 Palo Santo St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Angelica',
            'lname' => 'Rodriguez',
            'email' => 'angelicarodriguez@gmail.com',
            'phone_number' => '+63 933-567-8901',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #7 Duhat St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Rafael',
            'lname' => 'Castro',
            'email' => 'rafaelcastro@gmail.com',
            'phone_number' => '+63 912-356-7890',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #10 Guava St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Sofia',
            'lname' => 'Gonzalez',
            'email' => 'sofia.gonzalez@gmail.com',
            'phone_number' => '+63 916-789-0123',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #4 Adamos St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Julius',
            'lname' => 'Soriano',
            'email' => 'julius.soriano@gmail.com',
            'phone_number' => '+63 987-654-3210',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #9 Fire Tree St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Amira',
            'lname' => 'Flores',
            'email' => 'amira.flores@gmail.com',
            'phone_number' => '+63 977-889-0123',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #8 Eucalyptus St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Jayson',
            'lname' => 'Lopez',
            'email' => 'jaysonlopez@gmail.com',
            'phone_number' => '+63 998-877-6655',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #5 Balete St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-02-02 04:18:09',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'fname' => 'Kristine',
            'lname' => 'Ramos',
            'email' => 'kristineramos@gmail.com',
            'phone_number' => '+639377889900',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #11 Herbabuena St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Eliza',
            'lname' => 'Tantoco',
            'email' => 'elizasantoco@gmail.com',
            'phone_number' => '+639722998811',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #1 Fedrico St. (Long Rd, Upper)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Julian',
            'lname' => 'Diaz',
            'email' => 'juliandiaz@gmail.com',
            'phone_number' => '+639134678923',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #13 Jacaranda St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Emilio',
            'lname' => 'Serrano',
            'email' => 'emilioserrano@gmail.com',
            'phone_number' => '+639125667788',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #14 Kalatsuchi St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Nina',
            'lname' => 'Castro',
            'email' => 'ninacastro@gmail.com',
            'phone_number' => '+639337890123',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #20 Rimas St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Salvador',
            'lname' => 'Garcia',
            'email' => 'salvadorgarcia@gmail.com',
            'phone_number' => '+639876544321',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #6 Casoy St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Patricia',
            'lname' => 'Rivera',
            'email' => 'patriciarivera@gmail.com',
            'phone_number' => '+639984122334',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #17 Anis St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Marco',
            'lname' => 'Velasquez',
            'email' => 'marcovelasquez@gmail.com',
            'phone_number' => '+639717834568',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #16 Talong St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Ronald',
            'lname' => 'Campos',
            'email' => 'ronaldcampos@gmail.com',
            'phone_number' => '+639994345789',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #23 Palmera St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Andrea',
            'lname' => 'Chavez',
            'email' => 'andreachavez@gmail.com',
            'phone_number' => '+639712345679',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #2 Kalachuchi St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Freddie',
            'lname' => 'Rodriguez',
            'email' => 'freddierodriguez@gmail.com',
            'phone_number' => '+639163442299',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #18 Talisay St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Edith',
            'lname' => 'Gonzales',
            'email' => 'edithgonzales@gmail.com',
            'phone_number' => '+639284633478',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #22 Mangga St. (Waterdam Rd, Lower)',
            'birthdate' => '2000-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
        
        User::create([
            'fname' => 'Rosa',
            'lname' => 'Martinez',
            'email' => 'rosamartinez@gmail.com',
            'phone_number' => '+639313789654',
            'city' => 'Olongapo City',
            'barangay' => 'Gordon Heights',
            'street' => 'Blk #24 Gabi St. (Waterdam Rd, Lower)',
            'birthdate' => '2001-02-02',
            'email_verified_at' => '2025-01-02 04:18:49',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
    }
}
