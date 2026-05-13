(function () {
	'use strict';

	const config = window.bafSavedTrips || {};
	const form = document.querySelector('[data-baf-saved-trip-form]');
	const list = document.querySelector('[data-baf-saved-trip-list]');

	if (!form || !list || !config.isLoggedIn) {
		return;
	}

	const status = document.querySelector('[data-baf-saved-trip-status]');
	const empty = document.querySelector('[data-baf-saved-trip-empty]');
	const count = document.querySelector('[data-baf-saved-trip-count]');
	const submit = document.querySelector('[data-baf-saved-trip-submit]');
	const reset = document.querySelector('[data-baf-saved-trip-reset]');
	const idInput = form.querySelector('[name="saved_trip_id"]');
	const consentInput = form.querySelector('[name="local_storage_consent"]');
	const fields = {
		departure_date: form.querySelector('[name="departure_date"]'),
		destination: form.querySelector('[name="destination"]'),
		note: form.querySelector('[name="note"]'),
		origin: form.querySelector('[name="origin"]'),
		placement_key: form.querySelector('[name="placement_key"]'),
		return_date: form.querySelector('[name="return_date"]'),
		travel_style: form.querySelector('[name="travel_style"]'),
		travelers: form.querySelector('[name="travelers"]'),
	};
	let items = [];

	const text = (value, fallback = '') => String(value || fallback).trim();
	const clamp = (value, min, max) => Math.max(min, Math.min(max, Number.parseInt(value, 10) || min));

	const clearNode = (node) => {
		while (node.firstChild) {
			node.removeChild(node.firstChild);
		}
	};

	const setStatus = (message, type) => {
		status.textContent = message || '';
		status.dataset.state = type || '';
	};

	const setLoading = (isLoading) => {
		form.classList.toggle('is-loading', isLoading);
		submit.disabled = isLoading;
	};

	const request = async (url, options = {}) => {
		const response = await fetch(url, {
			credentials: 'same-origin',
			...options,
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': config.nonce,
				...(options.headers || {}),
			},
		});

		let body = null;

		try {
			body = await response.json();
		} catch (error) {
			body = null;
		}

		if (!response.ok) {
			throw new Error(body && body.message ? body.message : config.strings.genericError);
		}

		return body;
	};

	const payloadFromForm = () => ({
		departure_date: text(fields.departure_date.value),
		destination: text(fields.destination.value),
		local_storage_consent: consentInput.checked,
		note: text(fields.note.value),
		origin: text(fields.origin.value),
		placement_key: text(fields.placement_key.value, 'flights_white_label_search'),
		return_date: text(fields.return_date.value),
		travel_style: text(fields.travel_style.value, 'balanced'),
		travelers: clamp(fields.travelers.value, 1, 12),
	});

	const metaLine = (item) => {
		const parts = [
			item.origin ? `${item.origin} to ${item.destination}` : item.destination,
			[item.departure_date, item.return_date].filter(Boolean).join(' to '),
			item.travelers ? `${item.travelers} traveler${item.travelers === 1 ? '' : 's'}` : '',
			item.travel_style ? item.travel_style.replace(/_/g, ' ') : '',
		].filter(Boolean);

		return parts.join(' - ');
	};

	const contextLine = (item) => {
		const context = item.context || {};
		const parts = [
			context.placement_name || context.placement_key,
			context.suggested_subid ? `SubID: ${context.suggested_subid}` : '',
			`Provider action: ${context.provider_action || 'not_executed'}`,
		].filter(Boolean);

		return parts.join(' - ');
	};

	const makeLink = (href, label) => {
		const link = document.createElement('a');
		link.className = 'baf-saved-trips__small-link';
		link.href = href;
		link.textContent = label;
		return link;
	};

	const fillForm = (item) => {
		idInput.value = String(item.id || '');
		fields.origin.value = text(item.origin);
		fields.destination.value = text(item.destination);
		fields.departure_date.value = text(item.departure_date);
		fields.return_date.value = text(item.return_date);
		fields.travelers.value = String(item.travelers || 2);
		fields.travel_style.value = text(item.travel_style, 'balanced');
		fields.placement_key.value = text(item.context && item.context.placement_key, 'flights_white_label_search');
		fields.note.value = text(item.note);
		consentInput.checked = true;
		reset.hidden = false;
		submit.textContent = 'Update trip intent';
		fields.destination.focus();
		setStatus(config.strings.loaded, 'success');
	};

	const resetForm = () => {
		form.reset();
		idInput.value = '';
		fields.travelers.value = '2';
		fields.travel_style.value = 'balanced';
		fields.placement_key.value = 'flights_white_label_search';
		reset.hidden = true;
		submit.textContent = 'Save trip intent';
		setStatus('', '');
	};

	const renderItem = (item) => {
		const article = document.createElement('article');
		const title = document.createElement('h3');
		const meta = document.createElement('p');
		const context = document.createElement('p');
		const actions = document.createElement('div');
		const resumeButton = document.createElement('button');
		const deleteButton = document.createElement('button');

		article.className = 'baf-saved-trips__card';
		title.textContent = text(item.title, `Saved trip ${item.id}`);
		meta.className = 'baf-saved-trips__meta';
		meta.textContent = metaLine(item);
		context.className = 'baf-saved-trips__context';
		context.textContent = contextLine(item);
		actions.className = 'baf-saved-trips__card-actions';

		resumeButton.type = 'button';
		resumeButton.className = 'baf-saved-trips__secondary-button';
		resumeButton.textContent = 'Resume';
		resumeButton.addEventListener('click', () => fillForm(item));

		deleteButton.type = 'button';
		deleteButton.className = 'baf-saved-trips__danger-button';
		deleteButton.textContent = 'Delete';
		deleteButton.addEventListener('click', () => deleteItem(item.id));

		actions.append(resumeButton);

		if (item.links && item.links.flights) {
			actions.append(makeLink(item.links.flights, 'Flight search'));
		}

		if (item.links && item.links.hotels) {
			actions.append(makeLink(item.links.hotels, 'Hotel search'));
		}

		if (item.links && item.links.planner) {
			actions.append(makeLink(item.links.planner, 'AI planner'));
		}

		actions.append(deleteButton);
		article.append(title, meta, context);

		if (item.note) {
			const note = document.createElement('p');
			note.className = 'baf-saved-trips__note';
			note.textContent = item.note;
			article.appendChild(note);
		}

		article.appendChild(actions);

		return article;
	};

	const renderList = () => {
		clearNode(list);

		count.textContent = items.length ? `${items.length} saved` : '';
		empty.hidden = items.length !== 0;

		items.forEach((item) => {
			list.appendChild(renderItem(item));
		});
	};

	const loadItems = async () => {
		setStatus(config.strings.loading, 'loading');

		try {
			items = await request(`${config.endpoint}?per_page=20`);
			renderList();
			setStatus('', '');

			if (config.resumeTripId) {
				const match = items.find((item) => Number(item.id) === Number(config.resumeTripId));

				if (match) {
					fillForm(match);
				}
			}
		} catch (error) {
			setStatus(error.message || config.strings.genericError, 'error');
		}
	};

	const saveItem = async () => {
		const payload = payloadFromForm();
		const itemId = Number.parseInt(idInput.value, 10) || 0;

		if (!payload.destination) {
			setStatus('Add a destination before saving.', 'error');
			return;
		}

		if (!payload.local_storage_consent) {
			setStatus('Confirm local storage before saving.', 'error');
			return;
		}

		setLoading(true);
		setStatus('Saving trip intent...', 'loading');

		try {
			const saved = await request(itemId ? `${config.endpoint}/${itemId}` : config.endpoint, {
				body: JSON.stringify(payload),
				method: 'POST',
			});
			const existingIndex = items.findIndex((item) => Number(item.id) === Number(saved.id));

			if (existingIndex >= 0) {
				items[existingIndex] = saved;
			} else {
				items.unshift(saved);
			}

			renderList();
			resetForm();
			setStatus(itemId ? config.strings.updated : config.strings.saved, 'success');
		} catch (error) {
			setStatus(error.message || config.strings.genericError, 'error');
		} finally {
			setLoading(false);
		}
	};

	const deleteItem = async (itemId) => {
		if (!window.confirm(config.strings.confirmDelete)) {
			return;
		}

		setStatus('Deleting saved trip intent...', 'loading');

		try {
			await request(`${config.endpoint}/${itemId}`, { method: 'DELETE' });
			items = items.filter((item) => Number(item.id) !== Number(itemId));
			renderList();

			if (Number.parseInt(idInput.value, 10) === Number(itemId)) {
				resetForm();
			}

			setStatus(config.strings.deleted, 'success');
		} catch (error) {
			setStatus(error.message || config.strings.genericError, 'error');
		}
	};

	form.addEventListener('submit', (event) => {
		event.preventDefault();
		saveItem();
	});

	reset.addEventListener('click', resetForm);
	loadItems();
})();
