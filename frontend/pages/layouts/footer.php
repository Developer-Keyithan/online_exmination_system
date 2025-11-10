<footer id="footer"
    class="fixed bottom-0 flex flex-row items-center text-white font-semibold justify-between px-3 py-1 bg-[#0003] border-[#fff6] border-t md:rounded-tr-2xl backdrop-blur transition-all duration-200 max-h-10 z-[999999] <?php echo $collapse ? 'w-[calc(100%-4rem)] group-hover:w-[calc(100%-15rem)]' : 'w-[calc(100%-15rem)]' ?>">
    <a href="<?php echo config('app.powered-url') ?>" target="_blank"><?php echo config('app.powered-text') ?></a>
    <p class="hidden md:inline-block">&COPY; <?php echo config('app.copyright') ?></p>
    <p>Version : V.<?php echo config('app.version') ?></p>
</footer>