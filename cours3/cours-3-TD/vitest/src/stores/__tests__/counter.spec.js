import { setActivePinia, createPinia } from 'pinia'
import { describe, it, expect, beforeEach } from 'vitest'
import { useCounterStore } from '../Counter'

describe('Counter Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('incrémente la valeur correctement', () => {
    const store = useCounterStore()
    store.increment()
    expect(store.count).toBe(1)
  })

  it('décrémente la valeur correctement', () => {
    const store = useCounterStore()
    store.decrement()
    expect(store.count).toBe(-1)
  })
})