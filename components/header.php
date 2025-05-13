<?php
 session_start();
 $profID = $_SESSION['profID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tailwind Layout</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet"/>
</head>
<body class="min-h-screen flex flex-col" style="background-color: #C8C6D7;">

  <!-- Header -->
  <header class="py-4 px-6 shadow-md" style="background-color: #4A4063;">
    <div class="container mx-auto flex justify-between items-center">
      <a href="#" class="text-2xl font-bold text-white">Your Logo</a>
      <nav class="hidden md:block">
        <ul class="flex space-x-6">
          <li><a href="#" class="text-white hover:text-[#C8C6D7]">Home</a></li>
          <li><a href="#" class="text-white hover:text-[#C8C6D7]">About</a></li>
          <li><a href="#" class="text-white hover:text-[#C8C6D7]">Contact</a></li>
        </ul>
      </nav>
      <div class="md:hidden">
        <button class="focus:outline-none">
          <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>
  </header>

  <!-- Main content -->
  <main class="flex-grow py-12 px-12">
