import { i18nVue } from 'laravel-vue-i18n'

interface I18nVueOptions {
    resolve: (lang: string) => Promise<Record<string, any>>;
}

export function install(app: any) {
    app.use(i18nVue, {
        resolve: async (lang: string): Promise<Record<string, any>> => {
            const langs: Record<string, () => Promise<Record<string, any>>> = import.meta.glob('../../lang/*.json')

            return await langs[`../../lang/${lang}.json`]()
        },
    } as I18nVueOptions)
}