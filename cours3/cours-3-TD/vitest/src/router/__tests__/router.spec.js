import { describe, it, expect } from 'vitest'
import { createRouter, createWebHistory } from 'vue-router'
import { routes } from '../index.js'

describe('Router', () => {
  it('navigue vers la route par défaut', async () => {
    const router = createRouter({
      history: createWebHistory(),
      routes: routes,
    })
    router.push('/')
    await router.isReady()
    expect(router.currentRoute.value.path).toBe('/')
  })
})