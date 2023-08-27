package goi18n

import (
	"errors"
	"fmt"
	"sync"

	"github.com/pelletier/go-toml"
)

var (
	defaultLanguage = "en"
)

// locale is a language.
type locale struct {
	mu          sync.RWMutex
	id          int
	lang        string
	langDesc    string
	translation map[string]string
}

// Option is the option of Goi18n.
type Option struct {
	Path     string
	Language string
}

// Goi18n is a struct for i18n.
type Goi18n struct {
	mu        sync.RWMutex
	langs     []string
	langDescs map[string]string
	localeMap map[string]*locale
	option    *Option
}

// New creates a new Goi18n.
func New(opt *Option) *Goi18n {
	if opt == nil {
		opt = DefaultOption()
	}

	g := &Goi18n{
		mu:        sync.RWMutex{},
		langs:     make([]string, 0),
		langDescs: make(map[string]string),
		localeMap: make(map[string]*locale),
		option:    opt,
	}

	//fmt.Printf("Goi18n.New: %#v\n", opt)

	return g
}

// SetLangDesc sets the language description.
func (g *Goi18n) SetLangDesc(lang string, desc string) {
	g.langDescs[lang] = desc
}

// SetLanguage sets the language.
func (g *Goi18n) SetLanguage(lang string, desc string) bool {
	if g.IsExist(lang) {
		g.langDescs[lang] = desc
		if err := g.Reload(lang); err != nil {
			fmt.Printf("Goi18n.SetLanguage: %v\n", err)
			return false
		}
		return true
	} else {
		return g.add(&locale{lang: lang, langDesc: desc})
	}
}

// T returns the translation of key by default language.
func (g *Goi18n) T(key string) string {
	if g.IsExist(g.option.Language) || g.add(&locale{lang: g.option.Language}) {
		value, _ := g.localeMap[g.option.Language].Get(key)
		return value
	}
	return key
}

// Translate translate by given language.
func (g *Goi18n) Translate(lang string, key string) string {
	if g.IsExist(lang) || g.add(&locale{lang: lang}) {
		value, _ := g.localeMap[lang].Get(key)
		return value
	}
	return key
}

// GetLanguageLength returns the length of languages.
func (g *Goi18n) GetLanguageLength() int {
	g.mu.RLock()
	defer g.mu.RUnlock()
	return len(g.langs)
}

// DefaultOption returns the default option.
func DefaultOption() *Option {
	return &Option{
		Path:     "i18n",
		Language: defaultLanguage,
	}
}

// add add a language.
func (g *Goi18n) add(lc *locale) bool {
	if _, ok := g.localeMap[lc.lang]; ok {
		return false
	}

	g.mu.Lock()
	defer g.mu.Unlock()
	if err := lc.Reload(g.option.Path); err != nil {
		return false
	}
	lc.id = len(g.localeMap)
	g.localeMap[lc.lang] = lc
	g.langs = append(g.langs, lc.lang)
	g.langDescs[lc.lang] = lc.langDesc
	return true
}

// Reload locales
func (g *Goi18n) Reload(langs ...string) error {
	g.mu.Lock()
	defer g.mu.Unlock()
	if len(langs) == 0 {
		for _, lc := range g.localeMap {
			lc.mu.Lock()
			defer lc.mu.Unlock()
			lc.Reload(g.option.Path)
		}
	} else {
		for _, lang := range langs {
			if lc, ok := g.localeMap[lang]; ok {
				lc.mu.Lock()
				defer lc.mu.Unlock()
				err := lc.Reload(g.option.Path)
				if err != nil {
					return err
				}
			}
		}
	}

	return nil
}

// ListLangs returns all languages.
func (g *Goi18n) ListLangs() []string {
	g.mu.RLock()
	defer g.mu.RUnlock()
	langs := make([]string, len(g.langs))
	copy(langs, g.langs)
	return langs
}

// ListLangDescs returns all language descriptions.
func (g *Goi18n) ListLangDescs() []string {
	g.mu.RLock()
	defer g.mu.RUnlock()
	langDescs := make([]string, len(g.langDescs))
	for i, lang := range g.ListLangs() {
		langDescs[i] = g.langDescs[lang]
	}
	return langDescs
}

// IsExist returns whether the language is exist.
func (g *Goi18n) IsExist(lang string) bool {
	g.mu.RLock()
	defer g.mu.RUnlock()
	_, ok := g.localeMap[lang]
	return ok
}

// IndexLang returns the index of language.
func (g *Goi18n) IndexLang(lang string) int {
	if g.IsExist(lang) {
		return g.localeMap[lang].id
	}
	return -1
}

// GetLangByIndex Get language by index id.
func (g *Goi18n) GetLangByIndex(index int) string {
	if index < 0 || index >= len(g.langs) {
		return ""
	}

	g.mu.RLock()
	defer g.mu.RUnlock()
	return g.langs[index]
}

// GetDescriptionByIndex GetLangDescByIndex Get language description by index id.
func (g *Goi18n) GetDescriptionByIndex(index int) string {
	if index < 0 || index >= len(g.langs) {
		return ""
	}

	g.mu.RLock()
	defer g.mu.RUnlock()
	return g.langDescs[g.langs[index]]
}

// GetDescriptionByLang GetLangDescByLang Get language description by language.
func (g *Goi18n) GetDescriptionByLang(lang string) string {
	if g.IsExist(lang) {
		g.mu.RLock()
		defer g.mu.RUnlock()
		return g.langDescs[lang]
	}
	return ""
}

// Reload locale
func (l *locale) Reload(path string) error {
	filename := fmt.Sprintf("%s/%s.toml", path, l.lang)
	translation, err := toml.LoadFile(filename)
	if err != nil {
		fmt.Printf("Goi18n.Reload: %v\n", err)
		return err
	}

	if translation == nil {
		return errors.New("translation is nil")
	}

	if l.translation == nil {
		l.translation = make(map[string]string)
	}

	for key, value := range translation.ToMap() {
		l.translation[key] = value.(string)
	}
	return nil
}

// Get returns the translation of the key.
func (l *locale) Get(key string) (string, bool) {
	l.mu.RLock()
	defer l.mu.RUnlock()
	if value, ok := l.translation[key]; ok {
		return value, true
	}
	return key, false
}
