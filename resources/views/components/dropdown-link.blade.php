<a {{ $attributes->merge([
    'class' => 'block w-full px-4 py-2 text-start text-sm leading-5 
text-black bg-white 
dark:text-black dark:bg-white 
hover:bg-black dark:hover:bg-black 
hover:text-white dark:hover:text-white 
focus:outline-none focus:bg-white dark:focus:bg-white transition duration-150 ease-in-out'
]) }}>{{ $slot }}</a>