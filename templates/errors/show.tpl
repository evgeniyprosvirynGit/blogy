{extends file="layouts/main.tpl"}

{block name=content}
    <main class="mx-auto max-w-3xl px-4 py-16 text-center sm:px-6">
        <section class="rounded-[2rem] border border-slate-200 bg-white px-6 py-12 shadow-sm sm:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Error {$statusCode}</p>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-900">{$title}</h1>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-8 text-slate-600">
                {$message}
            </p>
            <a
                href="/"
                class="mt-8 inline-flex h-12 items-center justify-center rounded-full border border-slate-300 px-5 text-sm font-semibold text-slate-900 transition hover:border-slate-900 hover:bg-slate-900 hover:text-white"
            >
                Go to homepage
            </a>
        </section>
    </main>
{/block}
