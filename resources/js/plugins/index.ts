export function installPlugins(app: any, debug: boolean = false) {
    const plugins = import.meta.glob('./*.ts', { eager: true })

    Object.values(plugins).forEach((module) => {
        if (typeof module === 'object' && module !== null && 'install' in module && typeof module.install === 'function') {
            if (debug) {
                console.log('Installing plugin:', module)
            }

            module.install(app)
        }
    })
}