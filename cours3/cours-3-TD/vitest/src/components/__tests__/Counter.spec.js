import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { describe, it, expect, beforeEach } from 'vitest'
import Counter from '../CounterComponent.vue'

describe('CounterComponent.vue', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('affiche la valeur initiale du store et réagit aux clics', async () => {
    const wrapper = mount(Counter)
    expect(wrapper.find('#counter').text()).toBe('0')
    
    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click')
    
    expect(wrapper.find('#counter').text()).toBe('1')
    await buttons[0].trigger('click')
    expect(wrapper.find('#counter').text()).toBe('0')
  })
})